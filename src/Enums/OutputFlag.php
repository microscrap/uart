<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * c_oflag bitmask constants from <termios.h> (Linux / POSIX).
 * These control how the kernel processes outgoing bytes.
 */
enum OutputFlag: int
{
    case OPOST  = 0x0001;
    case OLCUC  = 0x0002;
    case ONLCR  = 0x0004;
    case OCRNL  = 0x0008;
    case ONOCR  = 0x0010;
    case ONLRET = 0x0020;
    case OFILL  = 0x0040;
    case OFDEL  = 0x0080;
}
