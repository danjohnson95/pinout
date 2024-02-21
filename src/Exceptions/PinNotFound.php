<?php

namespace DanJohnson95\Pinout\Exceptions;

use Exception;

class PinNotFound extends Exception
{
    public function __construct(int $pinNumber)
    {
        parent::__construct("Pin {$pinNumber} not found");
    }
}
