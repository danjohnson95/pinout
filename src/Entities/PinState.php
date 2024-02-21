<?php

namespace DanJohnson95\Pinout\Entities;

use DanJohnson95\Pinout\Enums\Level;

class PinState
{
    public function __construct(
        public int $pin,
        public Level $level,
        public int $fsel,
        public string $func,
        public ?int $alt,
    ) {
    }
}
