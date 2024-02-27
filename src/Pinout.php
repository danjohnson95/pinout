<?php

namespace DanJohnson95\Pinout;

use DanJohnson95\Pinout\PinManager;
use DanJohnson95\Pinout\Testing\PinoutFake;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DanJohnson95\Pinout\Entities\Pin get(int $pinNumber)
 * @method static \DanJohnson95\Pinout\Collections\PinCollection getAll(?array $pinNumbers)
 * @method static \DanJohnson95\Pinout\Entities\Pin setLevel(int $pinNumber, \DanJohnson95\Pinout\Enums\Level $level)
 * @method static \DanJohnson95\Pinout\Entities\Pin setFunction(int $pinNumber, \DanJohnson95\Pinout\Enums\Func $func)
 */
class Pinout extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return PinManager::class;
    }

    public static function fake()
    {
        static::swap($fake = new PinoutFake());

        return $fake;
    }
}
