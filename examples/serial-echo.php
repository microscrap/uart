<?php

/**
 * serial-echo.php — loopback echo test for a UART / serial port.
 *
 * Wiring: connect TX → RX on the adapter (loopback jumper), or use two
 *         USB-serial adapters wired TX↔RX and run this script twice.
 *
 * Usage:
 *   php examples/serial-echo.php /dev/ttyUSB0 115200
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

use Microscrap\Bindings\UART\Enums\BaudRate;
use Microscrap\Bindings\UART\Enums\TermiosQueue;

$path = $argv[1] ?? '/dev/ttyUSB0';
$baudArg = (int) ($argv[2] ?? 115200);

$baud = BaudRate::tryFrom(
    array_search(
        $baudArg,
        array_column(
            array_map(fn($c) => ['name' => $c->name, 'value' => $c->value], BaudRate::cases()),
            'value',
            'name'
        )
    )
) ?? BaudRate::B115200;

// ── Open ──────────────────────────────────────────────────────────────────────
echo "Opening {$path} at {$baudArg} baud...\n";

$port = uart_open($path, $baud);
if ($port === null) {
    fwrite(STDERR, "Failed to open {$path}. Check permissions (sudo usermod -aG dialout \$USER).\n");
    exit(1);
}

echo "Opened: fd={$port->fd}  path={$port->path}  baud={$port->baud->name}\n";

// Discard any stale data in both queues before starting.
uart_flush($port, TermiosQueue::TCIOFLUSH);

// ── Write ─────────────────────────────────────────────────────────────────────
$message = "Hello from microscrap/uart!\n";
$written = uart_write($port, $message);
echo "Wrote {$written} bytes: " . trim($message) . "\n";

// Block until the kernel has pushed all bytes out of the TX FIFO.
uart_drain($port);

// ── Read ──────────────────────────────────────────────────────────────────────
// Give the remote end (or loopback) a moment to echo back.
usleep(50_000);

$response = uart_read($port, strlen($message));
if ($response === false) {
    fwrite(STDERR, "Read failed.\n");
    uart_close($port);
    exit(1);
}

echo 'Read  ' . strlen($response) . ' bytes: ' . trim($response) . "\n";

$match = ($response === $message) ? 'PASS' : 'FAIL';
echo "Loopback check: {$match}\n";

// ── Close ─────────────────────────────────────────────────────────────────────
uart_close($port);
echo "Port closed.\n";
