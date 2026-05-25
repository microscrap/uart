<?php

use Microscrap\Bindings\UART\Termios;
use Microscrap\Bindings\UART\DataObjects\UARTPort;
use Microscrap\Bindings\UART\Enums\BaudRate;
use Microscrap\Bindings\UART\Enums\TermiosAction;

if (! function_exists('uart_tcgetattr')) {
    function uart_tcgetattr(UARTPort $port): array|false
    {
        return Termios::uartTcgetattr($port);
    }
}

if (! function_exists('uart_tcsetattr')) {
    function uart_tcsetattr(UARTPort $port, array $termios, TermiosAction $action = TermiosAction::TCSANOW): int
    {
        return Termios::uartTcsetattr($port, $termios, $action);
    }
}

if (! function_exists('uart_cfsetispeed')) {
    function uart_cfsetispeed(array $termios, BaudRate $baud): array|false
    {
        return Termios::uartCfsetispeed($termios, $baud);
    }
}

if (! function_exists('uart_cfsetospeed')) {
    function uart_cfsetospeed(array $termios, BaudRate $baud): array|false
    {
        return Termios::uartCfsetospeed($termios, $baud);
    }
}

if (! function_exists('uart_cfgetispeed')) {
    function uart_cfgetispeed(array $termios): ?BaudRate
    {
        return Termios::uartCfgetispeed($termios);
    }
}

if (! function_exists('uart_cfgetospeed')) {
    function uart_cfgetospeed(array $termios): ?BaudRate
    {
        return Termios::uartCfgetospeed($termios);
    }
}
