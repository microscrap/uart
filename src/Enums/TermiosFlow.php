<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * Flow control action values for tcflow(2).
 */
enum TermiosFlow: int
{
    case TCOOFF = 0;
    case TCOON  = 1;
    case TCIOFF = 2;
    case TCION  = 3;
}
