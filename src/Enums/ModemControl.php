<?php

namespace Microscrap\Bindings\UART\Enums;

/** The modem-control ioctl requests (asm-generic/ioctls.h). */
enum ModemControl: int
{
    case TIOCMGET = 0x5415;

    case TIOCMBIS = 0x5416;

    case TIOCMBIC = 0x5417;
}
