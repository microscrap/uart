<?php

declare(ticks=1);

/**
 * ld2410c.php — LD2410C 24 GHz mmWave radar presence sensor
 *
 * Combines two signal paths for the same sensor:
 *
 *   GPIO path  — LD2410C OUT pin wired to GPIO chip 0 pin 22.
 *                The OUT pin is driven HIGH by the sensor whenever any target
 *                (moving or stationary) is detected.  This is instant and
 *                needs no protocol parsing.
 *
 *   UART path  — LD2410C TX wired to the Pi RX (/dev/ttyAMA0 or /dev/ttyUSB0).
 *                The sensor streams binary data frames continuously.  Parsing
 *                these gives moving distance, stationary distance, and energy
 *                per target — far richer than the OUT pin alone.
 *
 * Wiring (3.3V logic):
 *   LD2410C VCC → 3.3V
 *   LD2410C GND → GND
 *   LD2410C TX  → Pi RX  (UART RX pin, GPIO 15 / physical pin 10)
 *   LD2410C RX  → Pi TX  (UART TX pin, GPIO 14 / physical pin 8)  [optional]
 *   LD2410C OUT → GPIO 22 (physical pin 15)
 *
 * Prerequisites:
 *   1. Enable serial port hardware (raspi-config → Interface Options → Serial Port,
 *      answer YES to hardware, NO to login shell).
 *
 * Usage:
 *   php examples/ld2410c.php [/dev/ttyAMA0]
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

use Microscrap\Bindings\GPIO\Enums\LineBias;
use Microscrap\Bindings\GPIO\Enums\LineDirection;
use Microscrap\Bindings\GPIO\Enums\LineValue;
use Microscrap\Bindings\UART\Enums\TermiosQueue;

// ── Configuration ─────────────────────────────────────────────────────────────

const GPIO_CHIP_PATH = '/dev/gpiochip0';
const OUT_PIN        = 22;
const CONSUMER       = 'ld2410c';

// 256000 is the LD2410C factory default.  It has no standard POSIX B* constant,
// so uart_open passes it via the Linux BOTHER/termios2 ioctl path automatically.
$uart_path = $argv[1] ?? '/dev/ttyAMA0';
const UART_BAUD = 256000;

// ── LD2410C frame constants ───────────────────────────────────────────────────
//
// Basic data output frame layout (23 bytes total):
//
//   [0..3]  F4 F3 F2 F1   frame header
//   [4..5]  0D 00          data length = 13 (little-endian)
//   [6]     02             data type (02 = basic data, 01 = engineering data)
//   [7]     AA             data head
//   [8]     XX             target state: 00=none 01=moving 02=stationary 03=both
//   [9..10] XX XX          moving target distance (cm, LE)
//   [11]    XX             moving target energy (0–100)
//   [12..13] XX XX         stationary target distance (cm, LE)
//   [14]    XX             stationary target energy (0–100)
//   [15..16] XX XX         detection distance (cm, LE)
//   [17]    55             data tail byte 1
//   [18]    00             data tail byte 2
//   [19..22] F8 F7 F6 F5  frame end

const FRAME_HEADER       = "\xF4\xF3\xF2\xF1";
const FRAME_END          = "\xF8\xF7\xF6\xF5";
const FRAME_TOTAL_LEN    = 23;
const DATA_TYPE_BASIC    = 0x02;
const DATA_HEAD          = 0xAA;

const TARGET_NONE        = 0x00;
const TARGET_MOVING      = 0x01;
const TARGET_STATIONARY  = 0x02;
const TARGET_BOTH        = 0x03;

// ── Signal handling ───────────────────────────────────────────────────────────

$running = true;

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGINT,  static function () use (&$running): void { $running = false; });
    pcntl_signal(SIGTERM, static function () use (&$running): void { $running = false; });
}

// ── GPIO setup — OUT pin ──────────────────────────────────────────────────────

$chip = gpiod_chip_open(GPIO_CHIP_PATH);
if ($chip === null) {
    fwrite(STDERR, "Cannot open " . GPIO_CHIP_PATH . "\n");
    exit(1);
}

$settings = gpiod_line_settings_new();
gpiod_line_settings_set_direction($settings, LineDirection::Input);
gpiod_line_settings_set_bias($settings, LineBias::PullDown);

$line_cfg = gpiod_line_config_new();
gpiod_line_config_add_line_settings($line_cfg, [OUT_PIN], $settings);

$req_cfg = gpiod_request_config_new();
gpiod_request_config_set_consumer($req_cfg, CONSUMER);

$gpio_request = gpiod_chip_request_lines($chip, $req_cfg, $line_cfg);
if ($gpio_request === null) {
    fwrite(STDERR, "Cannot request GPIO pin " . OUT_PIN . "\n");
    gpiod_chip_close($chip);
    exit(1);
}

echo "GPIO OUT pin " . OUT_PIN . " claimed on " . GPIO_CHIP_PATH . "\n";

// ── UART setup ────────────────────────────────────────────────────────────────

$port = uart_open($uart_path, UART_BAUD);
if ($port === null) {
    fwrite(STDERR, "Cannot open {$uart_path} at " . UART_BAUD->name . "\n");
    gpiod_line_request_release($gpio_request);
    gpiod_chip_close($chip);
    exit(1);
}

uart_flush($port, TermiosQueue::TCIOFLUSH);
echo "UART {$port->path} open at {$port->baud} baud\n";
echo "\nMonitoring — press Ctrl+C to stop.\n\n";

// ── Frame parser ──────────────────────────────────────────────────────────────

/**
 * Parse a 23-byte LD2410C basic data frame.
 * Returns an associative array on success, null if the frame is malformed.
 *
 * @return array{state:int, moving_cm:int, moving_energy:int,
 *               static_cm:int, static_energy:int, detect_cm:int}|null
 */
function parse_ld2410c_frame(string $frame): ?array
{
    if (strlen($frame) !== FRAME_TOTAL_LEN) {
        return null;
    }
    if (substr($frame, 0, 4) !== FRAME_HEADER || substr($frame, -4) !== FRAME_END) {
        return null;
    }
    if (ord($frame[6]) !== DATA_TYPE_BASIC || ord($frame[7]) !== DATA_HEAD) {
        return null;
    }

    $vals = unpack('C1state/v1moving_cm/C1moving_energy/v1static_cm/C1static_energy/v1detect_cm', substr($frame, 8, 9));

    return $vals !== false ? $vals : null;
}

/**
 * Synchronise the read buffer to the next FRAME_HEADER boundary.
 * Returns the remainder of $buf starting at the header, or '' if not found.
 */
function sync_to_header(string $buf): string
{
    $pos = strpos($buf, FRAME_HEADER);
    return $pos !== false ? substr($buf, $pos) : '';
}

function describe_state(int $state): string
{
    return match ($state) {
        TARGET_NONE        => 'no target',
        TARGET_MOVING      => 'MOVING',
        TARGET_STATIONARY  => 'STATIONARY',
        TARGET_BOTH        => 'MOVING + STATIONARY',
        default            => "unknown (0x" . dechex($state) . ")",
    };
}

// ── Main loop ─────────────────────────────────────────────────────────────────

$buf        = '';
$last_gpio  = null;
$last_state = null;

while ($running) {
    if (function_exists('pcntl_signal_dispatch')) {
        pcntl_signal_dispatch();
    }

    // ── 1. Sample the OUT pin ──────────────────────────────────────────────────
    $gpio_val = gpiod_line_request_get_value($gpio_request, OUT_PIN);
    $presence = ($gpio_val === LineValue::Active);

    if ($gpio_val !== $last_gpio) {
        $last_gpio = $gpio_val;
        $label     = $presence ? '🟢 PRESENCE DETECTED' : '⚫ no presence';
        echo "[GPIO OUT] {$label}\n";
    }

    // ── 2. Read available UART bytes ───────────────────────────────────────────
    $chunk = uart_read($port, 64);
    if ($chunk !== false && $chunk !== '') {
        $buf .= $chunk;
    }

    // ── 3. Extract and parse complete frames ───────────────────────────────────
    while (true) {
        $buf = sync_to_header($buf);

        if (strlen($buf) < FRAME_TOTAL_LEN) {
            break;
        }

        $frame = substr($buf, 0, FRAME_TOTAL_LEN);
        $data  = parse_ld2410c_frame($frame);

        if ($data === null) {
            // Bad frame — skip one byte and re-sync.
            $buf = substr($buf, 1);
            continue;
        }

        // Consume the parsed frame.
        $buf = substr($buf, FRAME_TOTAL_LEN);

        $state = $data['state'];
        if ($state !== $last_state) {
            $last_state = $state;
            $desc       = describe_state($state);

            if ($state === TARGET_NONE) {
                echo "[UART]     {$desc}\n";
            } elseif ($state === TARGET_MOVING) {
                echo "[UART]     {$desc}  — {$data['moving_cm']} cm  (energy {$data['moving_energy']})\n";
            } elseif ($state === TARGET_STATIONARY) {
                echo "[UART]     {$desc} — {$data['static_cm']} cm  (energy {$data['static_energy']})\n";
            } else {
                echo "[UART]     {$desc}  — moving {$data['moving_cm']} cm (E={$data['moving_energy']})"
                   . "  stationary {$data['static_cm']} cm (E={$data['static_energy']})\n";
            }
        }
    }

    // Small yield to avoid pinning a CPU core.  The sensor outputs ~10 Hz.
    usleep(20_000);
}

// ── Cleanup ───────────────────────────────────────────────────────────────────

echo "\nStopped.\n";
uart_close($port);
gpiod_line_request_release($gpio_request);
gpiod_chip_close($chip);
