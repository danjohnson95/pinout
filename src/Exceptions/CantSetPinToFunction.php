<?php

namespace DanJohnson95\Pinout\Exceptions;

use DanJohnson95\Pinout\Enums\Func;

class CantSetPinToFunction extends \Exception
{
    public function __construct(int $pinNumber, Func $function)
    {
        parent::__construct("Can't set pin {$pinNumber} to function {$function}");
    }
}
