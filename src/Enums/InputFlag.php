<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * c_iflag bitmask constants from <termios.h> (Linux / POSIX).
 * These control how the kernel processes incoming bytes.
 */
enum InputFlag: int
{
    case IGNBRK  = 0x0001;
    case BRKINT  = 0x0002;
    case IGNPAR  = 0x0004;
    case PARMRK  = 0x0008;
    case INPCK   = 0x0010;
    case ISTRIP  = 0x0020;
    case INLCR   = 0x0040;
    case IGNCR   = 0x0080;
    case ICRNL   = 0x0100;
    case IUCLC   = 0x0200;
    case IXON    = 0x0400;
    case IXANY   = 0x0800;
    case IXOFF   = 0x1000;
    case IMAXBEL = 0x2000;
    case IUTF8   = 0x4000;
}
