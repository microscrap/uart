<?php

namespace Microscrap\Bindings\UART\Enums;

/**
 * Optional action values for tcsetattr(2) — when to apply the new settings.
 */
enum TermiosAction: int
{
    case TCSANOW   = 0;
    case TCSADRAIN = 1;
    case TCSAFLUSH = 2;
}
