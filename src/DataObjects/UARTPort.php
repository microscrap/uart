<?php

namespace Microscrap\Bindings\UART\DataObjects;

final readonly class UARTPort
{
    public function __construct(
        public int $fd,
        public string $path,
        public int $baud,
    ) {}
}
