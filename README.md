# microscrap/uart — Linux UART / Serial bindings for ScrapyardIO

PHP library that wraps the [**posi**](https://github.com/php-io-extensions/posi) extension with global helpers, enums, and data objects. Every helper delegates to a facade class under `Microscrap\Bindings\UART`.

This project provides PHP bindings to the Linux POSIX termios serial-port API, mirroring the public surface of the POSIX `<termios.h>` / `tcgetattr(2)` / `cfmakeraw(3)` family.

## Highlights

* Open a serial device (`/dev/ttyUSB0`, `/dev/ttyAMA0`, etc.) and configure it in one call
* Automatic raw-mode setup (`cfmakeraw` semantics) — no line buffering, no character processing
* Typed `BaudRate` enum covers the full `speed_t` range from `B50` to `B4000000`
* Enum-typed termios flag sets for `c_iflag`, `c_oflag`, `c_cflag`, `c_lflag`, and `c_cc`
* Full termios attribute inspection and mutation via `uart_tcgetattr` / `uart_tcsetattr`
* Drain, flush, and flow control through `uart_drain`, `uart_flush`, `uart_flow`
* Thin global `uart_*` helper API — all functions are `function_exists`-guarded

## Requirements

* PHP 8.3+
* Linux kernel (any modern version with `/dev/tty*` devices)
* **ext-posi** ^0.4.0 — install from [php-io-extensions/posi](https://github.com/php-io-extensions/posi)
* **microscrap/posix** ^0.4.0

## Installation

Confirm **ext-posi** is loaded:

```bash
php -m | grep posi
```

```bash
composer require microscrap/uart
```

Composer autoloads `src/Helpers/uart-serial.php` and `src/Helpers/uart-termios.php`, registering the global `uart_*` functions.

## Usage

Serial I/O is controlled through **global helper functions** (`uart_open`, `uart_write`, `uart_read`, etc.). All helpers delegate to facade classes and are only defined once (`function_exists` guard).

Enums live under `Microscrap\Bindings\UART\Enums`. The port handle is `Microscrap\Bindings\UART\DataObjects\UARTPort`.

---

### Example — send a command and read the response

```php
<?php

use Microscrap\Bindings\UART\Enums\BaudRate;
use Microscrap\Bindings\UART\Enums\TermiosQueue;

// Open /dev/ttyUSB0 at 115200 baud, configured for raw byte I/O.
$port = uart_open('/dev/ttyUSB0', BaudRate::B115200);
if ($port === null) {
    exit("Failed to open port\n");
}

// Discard stale data before starting.
uart_flush($port, TermiosQueue::TCIOFLUSH);

// Send a command.
uart_write($port, "AT\r\n");

// Block until all bytes have been clocked out.
uart_drain($port);

// Wait briefly then read the response.
usleep(50_000);
$response = uart_read($port, 128);
echo "Response: " . trim($response) . "\n";

uart_close($port);
```

---

### Example — manual termios configuration

```php
<?php

use Microscrap\Bindings\UART\Enums\BaudRate;
use Microscrap\Bindings\UART\Enums\ControlChar;
use Microscrap\Bindings\UART\Enums\ControlFlag;
use Microscrap\Bindings\UART\Enums\TermiosAction;

$port = uart_open('/dev/ttyAMA0', BaudRate::B9600);

// Read, mutate, and reapply termios attributes.
$t = uart_tcgetattr($port);

// Enable hardware flow control.
$t['c_cflag'] |= ControlFlag::CRTSCTS->value;

// Require at least 4 bytes per read() call.
$t['c_cc'][ControlChar::VMIN->value] = 4;

uart_tcsetattr($port, $t, TermiosAction::TCSADRAIN);

uart_close($port);
```

---

## API Reference

### Serial port lifecycle

| Helper | Facade method | Description |
|---|---|---|
| `uart_open(string $path, BaudRate $baud)` | `Serial::uartOpen` | Open and configure a port; returns `UARTPort\|null` |
| `uart_close(UARTPort $port)` | `Serial::uartClose` | Close the file descriptor; returns 0 or -1 |
| `uart_read(UARTPort $port, int $bytes)` | `Serial::uartRead` | Read up to `$bytes` bytes; returns `string\|false` |
| `uart_write(UARTPort $port, string $data)` | `Serial::uartWrite` | Write `$data`; returns bytes written |
| `uart_drain(UARTPort $port)` | `Serial::uartDrain` | Block until TX FIFO is empty (`tcdrain`) |
| `uart_flush(UARTPort $port, TermiosQueue $queue)` | `Serial::uartFlush` | Discard queued I/O (`tcflush`) |
| `uart_flow(UARTPort $port, TermiosFlow $action)` | `Serial::uartFlow` | Suspend / resume I/O (`tcflow`) |
| `uart_make_raw(array $termios)` | `Serial::makeRaw` | Apply `cfmakeraw` semantics to a termios array |

### Termios attribute access

| Helper | Facade method | Description |
|---|---|---|
| `uart_tcgetattr(UARTPort $port)` | `Termios::uartTcgetattr` | Read current attributes; returns `array\|false` |
| `uart_tcsetattr(UARTPort $port, array $t, TermiosAction $a)` | `Termios::uartTcsetattr` | Apply attributes; returns 0 or -1 |
| `uart_cfsetispeed(array $t, BaudRate $baud)` | `Termios::uartCfsetispeed` | Set input baud; returns new `array\|false` |
| `uart_cfsetospeed(array $t, BaudRate $baud)` | `Termios::uartCfsetospeed` | Set output baud; returns new `array\|false` |
| `uart_cfgetispeed(array $t)` | `Termios::uartCfgetispeed` | Decode input baud; returns `BaudRate\|null` |
| `uart_cfgetospeed(array $t)` | `Termios::uartCfgetospeed` | Decode output baud; returns `BaudRate\|null` |

### `UARTPort` data object

```php
final readonly class UARTPort {
    public int     $fd;    // open file descriptor
    public string  $path;  // device path (/dev/ttyUSB0)
    public BaudRate $baud; // configured baud rate
}
```

---

## Termios array format

`uart_tcgetattr` returns — and `uart_tcsetattr` / `uart_cfset*speed` consume — a plain PHP array:

```php
[
    'c_iflag' => int,       // InputFlag bitmask
    'c_oflag' => int,       // OutputFlag bitmask
    'c_cflag' => int,       // ControlFlag bitmask (includes baud rate bits)
    'c_lflag' => int,       // LocalFlag bitmask
    'c_cc'    => int[],     // ControlChar indexed array (NCCS elements)
]
```

---

## Enum quick reference

### `BaudRate`

`B0`, `B50`, `B75`, `B110`, `B134`, `B150`, `B200`, `B300`, `B600`, `B1200`, `B1800`, `B2400`, `B4800`, `B9600`, `B19200`, `B38400`, `B57600`, `B115200`, `B230400`, `B460800`, `B500000`, `B576000`, `B921600`, `B1000000`, `B1152000`, `B1500000`, `B2000000`, `B2500000`, `B3000000`, `B3500000`, `B4000000`

### `TermiosAction`

| Case | Value | When settings take effect |
|---|---|---|
| `TCSANOW` | 0 | Immediately |
| `TCSADRAIN` | 1 | After all output is transmitted |
| `TCSAFLUSH` | 2 | After output drained and input discarded |

### `TermiosQueue`

| Case | Value | What `tcflush` discards |
|---|---|---|
| `TCIFLUSH` | 0 | Received but unread input |
| `TCOFLUSH` | 1 | Written but untransmitted output |
| `TCIOFLUSH` | 2 | Both queues |

### `TermiosFlow`

| Case | Value | Effect |
|---|---|---|
| `TCOOFF` | 0 | Suspend output |
| `TCOON` | 1 | Resume output |
| `TCIOFF` | 2 | Send STOP character to device |
| `TCION` | 3 | Send START character to device |

### `InputFlag` — `c_iflag` bits

`IGNBRK`, `BRKINT`, `IGNPAR`, `PARMRK`, `INPCK`, `ISTRIP`, `INLCR`, `IGNCR`, `ICRNL`, `IUCLC`, `IXON`, `IXANY`, `IXOFF`, `IMAXBEL`, `IUTF8`

### `OutputFlag` — `c_oflag` bits

`OPOST`, `OLCUC`, `ONLCR`, `OCRNL`, `ONOCR`, `ONLRET`, `OFILL`, `OFDEL`

### `ControlFlag` — `c_cflag` bits

`CBAUD`, `CBAUDEX`, `CSIZE`, `CS5`, `CS6`, `CS7`, `CS8`, `CSTOPB`, `CREAD`, `PARENB`, `PARODD`, `HUPCL`, `CLOCAL`, `CRTSCTS`

### `LocalFlag` — `c_lflag` bits

`ISIG`, `ICANON`, `XCASE`, `ECHO`, `ECHOE`, `ECHOK`, `ECHONL`, `NOFLSH`, `TOSTOP`, `ECHOCTL`, `ECHOPRT`, `ECHOKE`, `FLUSHO`, `PENDIN`, `IEXTEN`, `EXTPROC`

### `ControlChar` — `c_cc` indices

`VINTR`, `VQUIT`, `VERASE`, `VKILL`, `VEOF`, `VTIME`, `VMIN`, `VSWTC`, `VSTART`, `VSTOP`, `VSUSP`, `VEOL`, `VREPRINT`, `VDISCARD`, `VWERASE`, `VLNEXT`, `VEOL2`

---

## Notes on `uart_open` defaults

`uart_open` applies the following configuration automatically:

* **`O_RDWR | O_NOCTTY`** — read/write access; device does not become the controlling terminal
* **`cfmakeraw` semantics** — no input/output processing, 8N1, no parity
* **`VMIN=1`, `VTIME=0`** — `read()` blocks until at least one byte arrives
* **`TCSANOW`** — settings applied immediately

To override any of these defaults, call `uart_tcgetattr`, mutate the returned array with the flag enums, and reapply with `uart_tcsetattr`.
