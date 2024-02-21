<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinStateCollection;
use DanJohnson95\Pinout\Entities\PinState;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;

interface Commandable
{
    public function getAll(?array $pinNumbers): PinStateCollection;

    public function get(int $pinNumber): PinState;

    public function setFunction(int $pinNumber, Func $func): self;

    public function setLevel(int $pinNumber, Level $level): self;
}
