<?php

namespace DanJohnson95\Pinout\Facades;

use DanJohnson95\Pinout\Services\PinService as ServicesPinService;
use DanJohnson95\Pinout\Testing\PinoutFake;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DanJohnson95\Pinout\Entities\Pin pin(int $pinNumber)
 * @method static \DanJohnson95\Pinout\Collections\PinCollection getAll(?array $pinNumbers)
 * @method static \DanJohnson95\Pinout\Entities\Pin setLevel(int $pinNumber, \DanJohnson95\Pinout\Enums\Level $level)
 * @method static \DanJohnson95\Pinout\Entities\Pin setFunction(int $pinNumber, \DanJohnson95\Pinout\Enums\Func $func)
 *
 * @method static void assertPinTurnedOff(int $pinNumber)
 * @method static void assertPinTurnedOn(int $pinNumber)
 */
class PinService extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return ServicesPinService::class;
    }

    public static function fake()
    {
        static::swap($fake = new PinoutFake());

        return $fake;
    }
}
