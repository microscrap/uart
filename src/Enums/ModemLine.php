<?php

namespace Microscrap\Bindings\UART\Enums;

/** The TIOCM_* bits for the modem control lines a port drives (asm-generic/termios.h). */
enum ModemLine: int
{
    case DTR = 0x002;

    case RTS = 0x004;
}
