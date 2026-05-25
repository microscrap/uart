<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * Standard POSIX baud rate constants (speed_t values from <termios.h>).
 * Values B0–B38400 are the classic POSIX set; B57600 and above are
 * Linux CBAUDEX extensions.
 */
enum BaudRate: int
{
    case B0       = 0;
    case B50      = 1;
    case B75      = 2;
    case B110     = 3;
    case B134     = 4;
    case B150     = 5;
    case B200     = 6;
    case B300     = 7;
    case B600     = 8;
    case B1200    = 9;
    case B1800    = 10;
    case B2400    = 11;
    case B4800    = 12;
    case B9600    = 13;
    case B19200   = 14;
    case B38400   = 15;

    // Linux CBAUDEX (bit 12 set)
    case B57600   = 4097;
    case B115200  = 4098;
    case B230400  = 4099;
    case B460800  = 4100;
    case B500000  = 4101;
    case B576000  = 4102;
    case B921600  = 4103;
    case B1000000 = 4104;
    case B1152000 = 4105;
    case B1500000 = 4106;
    case B2000000 = 4107;
    case B2500000 = 4108;
    case B3000000 = 4109;
    case B3500000 = 4110;
    case B4000000 = 4111;
}
