<?php

declare(strict_types=1);

if (! extension_loaded('posi')) {
    throw new RuntimeException('ext-posi is not loaded');
}

/** Pty masters ptyPair() opened; closed after each test. */
$GLOBALS['ptyMasters'] = [];

/**
 * A pty: [master fd, slave path]. TIOCSPTLCK unlocks the slave and TIOCGPTN
 * names it, both from <asm-generic/ioctls.h>.
 */
function ptyPair(): array
{
    $master = posix_open('/dev/ptmx', O_RDWR | O_NOCTTY | O_CLOEXEC | O_NONBLOCK);
    if ($master < 0) {
        throw new RuntimeException('opening /dev/ptmx failed, errno ' . posi_errno());
    }
    ioctl($master, 0x40045431, ['value' => 0], $unused);
    ioctl($master, 0x80045430, ['value' => 0], $number);
    $GLOBALS['ptyMasters'][] = $master;

    return [$master, "/dev/pts/{$number}"];
}

/** Reads what has arrived on $fd within $timeoutNs. */
function readArrived(int $fd, int $timeoutNs = 500_000_000): string
{
    return posix_ppoll($fd, $timeoutNs) === 1 ? (string) posix_read($fd, 256) : '';
}

/*
 * microscrap/uart's enums carry Linux <termios.h> values, so the suite runs on
 * Linux only; elsewhere every test is skipped.
 */
pest()->beforeEach(function (): void {
    if (PHP_OS_FAMILY !== 'Linux') {
        $this->markTestSkipped('microscrap/uart enums carry Linux termios values');
    }
})->afterEach(function (): void {
    foreach ($GLOBALS['ptyMasters'] as $fd) {
        posix_close($fd);
    }
    $GLOBALS['ptyMasters'] = [];
})->in(__DIR__);
