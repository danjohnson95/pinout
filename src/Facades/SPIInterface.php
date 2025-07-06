<?php

namespace DanJohnson95\Pinout\Facades;

use Illuminate\Support\Facades\Facade;

class SPIInterface extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return SPIInterface::class;
    }
}
