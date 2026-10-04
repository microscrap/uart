<?php

declare(strict_types=1);

use Microscrap\Bindings\UART\Enums\BaudRate;
use Microscrap\Bindings\UART\Enums\LocalFlag;
use Microscrap\Bindings\UART\Enums\ModemLine;
use Microscrap\Bindings\UART\Enums\TermiosFlow;
use Microscrap\Bindings\UART\Enums\TermiosQueue;

it('opens a port raw at the rate given and moves bytes both ways', function () {
    [$master, $slave] = ptyPair();

    $port = uart_open($slave, BaudRate::B115200);

    expect($port)->not->toBeNull()
        ->and(uart_write($port, "ping\r\n"))->toBe(6)
        ->and(readArrived($master))->toBe("ping\r\n");

    posix_write($master, "pong\n", 5);

    expect(readArrived($port->fd))->toBe("pong\n")
        ->and(uart_close($port))->toBe(0);
});

it('leaves the line raw: no canonical mode, no echo, VMIN 1 and VTIME 0', function () {
    [, $slave] = ptyPair();
    $port = uart_open($slave);
    $termios = uart_tcgetattr($port);

    expect($termios['c_lflag'] & (LocalFlag::ICANON->value | LocalFlag::ECHO->value))->toBe(0)
        ->and($termios['c_cc'][VMIN])->toBe(1)
        ->and($termios['c_cc'][VTIME])->toBe(0)
        ->and(uart_cfgetospeed($termios))->toBe(BaudRate::B9600);

    uart_close($port);
});

it('changes the rate on an open port, standard and BOTHER', function () {
    [, $slave] = ptyPair();
    $port = uart_open($slave);

    expect(uart_set_baud_rate($port, BaudRate::B57600))->toBe(0)
        ->and(uart_cfgetospeed(uart_tcgetattr($port)))->toBe(BaudRate::B57600)
        ->and(uart_set_baud_rate($port, 256000))->toBe(0)
        ->and(uart_cfgetospeed(uart_tcgetattr($port)))->toBeNull();

    $termios = uart_tcgetattr($port);

    expect(uart_tcsetattr($port, $termios))->toBe(0);

    uart_close($port);
});

it('sets speeds on a termios array through the BaudRate enum', function () {
    [, $slave] = ptyPair();
    $port = uart_open($slave);

    $termios = uart_cfsetospeed(uart_cfsetispeed(uart_tcgetattr($port), BaudRate::B19200), BaudRate::B19200);

    expect(uart_cfgetispeed($termios))->toBe(BaudRate::B19200)
        ->and(uart_tcsetattr($port, $termios))->toBe(0)
        ->and(uart_cfgetospeed(uart_tcgetattr($port)))->toBe(BaudRate::B19200);

    uart_close($port);
});

it('drains, flushes and controls flow', function () {
    [, $slave] = ptyPair();
    $port = uart_open($slave);

    expect(uart_drain($port))->toBe(0)
        ->and(uart_flush($port, TermiosQueue::TCIOFLUSH))->toBe(0)
        ->and(uart_flow($port, TermiosFlow::TCOON))->toBe(0);

    uart_close($port);
});

it('reports -1 for modem lines on a pty, which has none', function () {
    [, $slave] = ptyPair();
    $port = uart_open($slave);

    expect(uart_set_modem_line($port, ModemLine::DTR))->toBe(-1)
        ->and(uart_clear_modem_line($port, ModemLine::RTS))->toBe(-1)
        ->and(uart_modem_lines($port))->toBe(-1);

    uart_close($port);
});

it('returns null for a path that does not open or is not a terminal', function () {
    expect(uart_open('/dev/no-such-tty'))->toBeNull()
        ->and(uart_open('/dev/null'))->toBeNull();
});
