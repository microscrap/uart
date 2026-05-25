<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * c_cc array index constants from <termios.h> (Linux / POSIX).
 * Use these as indices into the 'c_cc' sub-array of a termios array.
 *
 * VMIN and VTIME are the most commonly used for raw serial I/O:
 *   c_cc[VMIN]  — minimum number of bytes read() should block for
 *   c_cc[VTIME] — timeout in tenths of a second (0 = no timeout)
 */
enum ControlChar: int
{
    case VINTR    = 0;
    case VQUIT    = 1;
    case VERASE   = 2;
    case VKILL    = 3;
    case VEOF     = 4;
    case VTIME    = 5;
    case VMIN     = 6;
    case VSWTC    = 7;
    case VSTART   = 8;
    case VSTOP    = 9;
    case VSUSP    = 10;
    case VEOL     = 11;
    case VREPRINT = 12;
    case VDISCARD = 13;
    case VWERASE  = 14;
    case VLNEXT   = 15;
    case VEOL2    = 16;
}
