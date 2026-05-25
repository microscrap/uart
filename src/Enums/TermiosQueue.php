<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * Queue selector values for tcflush(2).
 */
enum TermiosQueue: int
{
    case TCIFLUSH  = 0;
    case TCOFLUSH  = 1;
    case TCIOFLUSH = 2;
}
