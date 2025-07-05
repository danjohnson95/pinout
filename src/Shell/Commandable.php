<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;

interface Commandable
{
    public function getAll(array $pinNumbers): PinCollection;

    public function get(int $pinNumber): Pin;

    public function setFunction(int $pinNumber, Func $func): self;

    public function setLevel(int $pinNumber, Level $level): self;

    public function exportPin(int $pinNumber): self;
}
