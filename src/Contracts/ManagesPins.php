<?php

namespace DanJohnson95\Pinout\Contracts;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;

interface ManagesPins
{
    public function pin(int $pinNumber): Pin;
    public function pins(int ...$pinNumbers): PinCollection;
    public function setLevel(Pin $pin, Level $level): Pin;
    public function setFunction(Pin $pin, Func $func): Pin;
}
