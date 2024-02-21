<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinStateCollection;
use DanJohnson95\Pinout\Entities\PinState;

interface Commandable
{
    public function getAll(?array $pinNumbers): PinStateCollection;

    public function get(int $pinNumber): PinState;
}
