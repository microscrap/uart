<?php

use Microscrap\Bindings\UART\Serial;
use Microscrap\Bindings\UART\DataObjects\UARTPort;
use Microscrap\Bindings\UART\Enums\BaudRate;
use Microscrap\Bindings\UART\Enums\TermiosQueue;
use Microscrap\Bindings\UART\Enums\TermiosFlow;

if (! function_exists('uart_open')) {
    function uart_open(string $path, BaudRate|int $baud = BaudRate::B9600): ?UARTPort
    {
        return Serial::uartOpen($path, $baud);
    }
}

if (! function_exists('uart_set_baud_rate')) {
    function uart_set_baud_rate(UARTPort $port, BaudRate|int $baud): int
    {
        return Serial::uartSetBaudRate($port, $baud);
    }
}

if (! function_exists('uart_close')) {
    function uart_close(UARTPort $port): int
    {
        return Serial::uartClose($port);
    }
}

if (! function_exists('uart_read')) {
    function uart_read(UARTPort $port, int $bytes): string|false
    {
        return Serial::uartRead($port, $bytes);
    }
}

if (! function_exists('uart_write')) {
    function uart_write(UARTPort $port, string $data): int
    {
        return Serial::uartWrite($port, $data);
    }
}

if (! function_exists('uart_drain')) {
    function uart_drain(UARTPort $port): int
    {
        return Serial::uartDrain($port);
    }
}

if (! function_exists('uart_flush')) {
    function uart_flush(UARTPort $port, TermiosQueue $queue = TermiosQueue::TCIOFLUSH): int
    {
        return Serial::uartFlush($port, $queue);
    }
}

if (! function_exists('uart_flow')) {
    function uart_flow(UARTPort $port, TermiosFlow $action): int
    {
        return Serial::uartFlow($port, $action);
    }
}

if (! function_exists('uart_make_raw')) {
    function uart_make_raw(array $termios): array
    {
        return Serial::makeRaw($termios);
    }
}
