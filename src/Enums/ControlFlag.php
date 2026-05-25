<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * c_cflag bitmask constants from <termios.h> (Linux / POSIX).
 * These control the serial line hardware parameters.
 */
enum ControlFlag: int
{
    case CBAUD   = 0x100F;
    case CBAUDEX = 0x1000;

    // Character-size field — CSIZE (0x0030) is the 2-bit mask; CS8 equals
    // that mask numerically (all bits set), so PHP would reject both as
    // duplicate enum values.  CSIZE is omitted here; use CS8->value when
    // you need to clear the field (~CS8 masks both bits) or set it to 8N.
    case CS5     = 0x0000;
    case CS6     = 0x0010;
    case CS7     = 0x0020;
    case CS8     = 0x0030;

    case CSTOPB  = 0x0040;
    case CREAD   = 0x0080;
    case PARENB  = 0x0100;
    case PARODD  = 0x0200;
    case HUPCL   = 0x0400;
    case CLOCAL  = 0x0800;

    case CRTSCTS = 0x80000000;
}
