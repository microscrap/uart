<?php

namespace Microscrap\Bindings\UART;

use Posi\Termios as Posi;
use Microscrap\Bindings\UART\DataObjects\UARTPort;
use Microscrap\Bindings\UART\Enums\BaudRate;
use Microscrap\Bindings\UART\Enums\TermiosAction;

class Termios
{
    /**
     * Read the current terminal attributes for the port's file descriptor.
     * Returns a termios array or false on failure.
     */
    public static function uartTcgetattr(UARTPort $port): array|false
    {
        return Posi::tcgetattr($port->fd);
    }

    /**
     * Apply a termios array to the port's file descriptor.
     * $action controls when the change takes effect (default: immediately).
     */
    public static function uartTcsetattr(UARTPort $port, array $termios, TermiosAction $action = TermiosAction::TCSANOW): int
    {
        return Posi::tcsetattr($port->fd, $action->value, $termios);
    }

    /**
     * Return a new termios array with the input baud rate set to $baud.
     */
    public static function uartCfsetispeed(array $termios, BaudRate $baud): array|false
    {
        return Posi::cfsetispeed($termios, $baud->value);
    }

    /**
     * Return a new termios array with the output baud rate set to $baud.
     */
    public static function uartCfsetospeed(array $termios, BaudRate $baud): array|false
    {
        return Posi::cfsetospeed($termios, $baud->value);
    }

    /**
     * Return the input baud rate from a termios array as a BaudRate enum.
     * Returns null for non-standard rates (e.g. 256000 set via setBaudRate /
     * BOTHER) — those are stored differently in the kernel and will not be
     * reflected by cfgetispeed.  Use uartGetAttributes() if you need the raw
     * numeric rate for a BOTHER-configured port.
     */
    public static function uartCfgetispeed(array $termios): ?BaudRate
    {
        return BaudRate::tryFrom(Posi::cfgetispeed($termios));
    }

    /**
     * Return the output baud rate from a termios array as a BaudRate enum.
     * Returns null for non-standard rates — same caveat as uartCfgetispeed.
     */
    public static function uartCfgetospeed(array $termios): ?BaudRate
    {
        return BaudRate::tryFrom(Posi::cfgetospeed($termios));
    }
}
