<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * c_lflag bitmask constants from <termios.h> (Linux / POSIX).
 * These control the terminal line discipline (canonical mode, echo, signals, etc.).
 */
enum LocalFlag: int
{
    case ISIG    = 0x0001;
    case ICANON  = 0x0002;
    case XCASE   = 0x0004;
    case ECHO    = 0x0008;
    case ECHOE   = 0x0010;
    case ECHOK   = 0x0020;
    case ECHONL  = 0x0040;
    case NOFLSH  = 0x0080;
    case TOSTOP  = 0x0100;
    case ECHOCTL = 0x0200;
    case ECHOPRT = 0x0400;
    case ECHOKE  = 0x0800;
    case FLUSHO  = 0x1000;
    case PENDIN  = 0x4000;
    case IEXTEN  = 0x8000;
    case EXTPROC = 0x10000;
}
