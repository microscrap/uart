<?php

namespace Microscrap\Bindings\UART;

use Posi\Termios as Posi;
use Microscrap\Bindings\POSIX\Enums\FileControlFlag;
use Microscrap\Bindings\UART\DataObjects\UARTPort;
use Microscrap\Bindings\UART\Enums\BaudRate;
use Microscrap\Bindings\UART\Enums\ControlChar;
use Microscrap\Bindings\UART\Enums\ControlFlag;
use Microscrap\Bindings\UART\Enums\InputFlag;
use Microscrap\Bindings\UART\Enums\LocalFlag;
use Microscrap\Bindings\UART\Enums\OutputFlag;
use Microscrap\Bindings\UART\Enums\TermiosAction;
use Microscrap\Bindings\UART\Enums\TermiosQueue;
use Microscrap\Bindings\UART\Enums\TermiosFlow;

class Serial
{
    /**
     * Open a serial port, configure it for raw byte I/O at the given baud rate,
     * and return a UARTPort handle.
     *
     * $baud accepts either a BaudRate enum case (for standard POSIX rates) or a
     * plain integer (for non-standard rates like 256000 that require the Linux
     * BOTHER/termios2 path).  On non-Linux systems, non-standard integers will
     * cause setBaudRate to return -1 and the open to fail.
     *
     * Opens with O_RDWR | O_NOCTTY so the kernel does not make this process
     * the controlling terminal of the device.
     */
    public static function uartOpen(string $path, BaudRate|int $baud = BaudRate::B9600): ?UARTPort
    {
        // Posi::setBaudRate expects the actual baud rate integer (9600, 115200…),
        // not the POSIX speed_t constant stored in BaudRate::value (13, 4098…).
        // Extract it from the enum case name by stripping the leading 'B'.
        $baudInt = $baud instanceof BaudRate ? (int) ltrim($baud->name, 'B') : $baud;

        $flags = FileControlFlag::O_RDWR->value | FileControlFlag::O_NOCTTY->value;
        $fd = posix_open($path, $flags);
        if ($fd < 0) {
            return null;
        }

        $termios = Posi::tcgetattr($fd);
        if ($termios === false) {
            posix_close($fd);
            return null;
        }

        $termios = self::makeRaw($termios);
        if (Posi::tcsetattr($fd, TermiosAction::TCSANOW->value, $termios) !== 0) {
            posix_close($fd);
            return null;
        }

        // setBaudRate handles both standard B* rates and arbitrary rates
        // (e.g. 256000) via the Linux BOTHER/termios2 ioctl path.
        if (Posi::setBaudRate($fd, $baudInt) !== 0) {
            posix_close($fd);
            return null;
        }

        return new UARTPort($fd, $path, $baudInt);
    }

    public static function uartClose(UARTPort $port): int
    {
        return posix_close($port->fd);
    }

    public static function uartRead(UARTPort $port, int $bytes): string|false
    {
        return posix_read($port->fd, $bytes);
    }

    public static function uartWrite(UARTPort $port, string $data): int
    {
        return posix_write($port->fd, $data, strlen($data));
    }

    /**
     * Change the baud rate on an already-open port.
     * Accepts any numeric rate; non-standard values use the Linux BOTHER path.
     */
    public static function uartSetBaudRate(UARTPort $port, BaudRate|int $baud): int
    {
        $baudInt = $baud instanceof BaudRate ? (int) ltrim($baud->name, 'B') : $baud;
        return Posi::setBaudRate($port->fd, $baudInt);
    }

    /**
     * Block until all output has been transmitted to the device.
     */
    public static function uartDrain(UARTPort $port): int
    {
        return Posi::tcdrain($port->fd);
    }

    /**
     * Discard queued but untransmitted / unreceived data.
     */
    public static function uartFlush(UARTPort $port, TermiosQueue $queue = TermiosQueue::TCIOFLUSH): int
    {
        return Posi::tcflush($port->fd, $queue->value);
    }

    /**
     * Suspend or resume I/O transmission.
     */
    public static function uartFlow(UARTPort $port, TermiosFlow $action): int
    {
        return Posi::tcflow($port->fd, $action->value);
    }

    /**
     * Apply cfmakeraw(3) semantics to a termios array — disables all processing
     * so bytes are passed through unchanged in both directions.
     *
     * Sets VMIN=1 / VTIME=0: read() returns as soon as at least one byte arrives.
     */
    public static function makeRaw(array $termios): array
    {
        $termios['c_iflag'] &= ~(
            InputFlag::IGNBRK->value  |
            InputFlag::BRKINT->value  |
            InputFlag::PARMRK->value  |
            InputFlag::ISTRIP->value  |
            InputFlag::INLCR->value   |
            InputFlag::IGNCR->value   |
            InputFlag::ICRNL->value   |
            InputFlag::IXON->value
        );

        $termios['c_oflag'] &= ~OutputFlag::OPOST->value;

        $termios['c_lflag'] &= ~(
            LocalFlag::ECHO->value    |
            LocalFlag::ECHONL->value  |
            LocalFlag::ICANON->value  |
            LocalFlag::ISIG->value    |
            LocalFlag::IEXTEN->value
        );

        // CS8->value (0x0030) equals the CSIZE mask, so negating it clears
        // the character-size field before setting it to 8-bit.
        $termios['c_cflag'] &= ~(ControlFlag::CS8->value | ControlFlag::PARENB->value);
        $termios['c_cflag'] |= ControlFlag::CS8->value;

        $termios['c_cc'][ControlChar::VMIN->value]  = 1;
        $termios['c_cc'][ControlChar::VTIME->value] = 0;

        return $termios;
    }
}
