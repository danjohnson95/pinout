<?php

namespace DanJohnson95\Pinout\Testing;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Contracts\ManagesPins;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;

use function PHPUnit\Framework\assertSame;

class PinoutFake implements ManagesPins
{
    protected PinCollection $fakePins;

    public function __construct()
    {
        $this->fakePins = PinCollection::make();
    }

    public function pin(int $pinNumber): Pin
    {
        if (!$pin = $this->fakePins->findByPinNumber($pinNumber)) {
            $pin = Pin::make(
                pinNumber: $pinNumber,
                level: Level::LOW,
                func: Func::OUTPUT,
            );

            $this->fakePins->push($pin);
        }

        return $pin;
    }

    public function pins(int ...$pinNumbers): PinCollection
    {
        return PinCollection::make(
            collect($pinNumbers)->map(fn (int $int) => $this->pin($int))
        );
    }

    public function setLevel(Pin $pin, Level $level): Pin
    {
        $pinNumberToUpdate = $pin->pinNumber;

        $this->fakePins = $this->fakePins->map(function (Pin $p) use ($level, $pinNumberToUpdate) {
            if ($p->pinNumber === $pinNumberToUpdate) {
                $p->level = $level;
            }

            return $p;
        });

        return $this->fakePins->findByPinNumber($pinNumberToUpdate);
    }

    public function setFunction(Pin $pin, Func $func): Pin
    {
        $pinNumber = $pin->pinNumber;
        $this->fakePins = $this->fakePins
            ->map(function ($collectionPin) use ($pinNumber, $func) {
                if ($collectionPin->pinNumber !== $pinNumber) {
                    return $collectionPin;
                }

                return Pin::make(
                    pinNumber: $pinNumber,
                    level: Level::LOW,
                    func: $func,
                );
            });
        
        return $this->fakePins->findByPinNumber($pinNumber);
    }

    public function assertPinIsInput(int $pinNumber)
    {
        assertSame(
            Func::INPUT,
            $this->fakePins->findByPinNumber($pinNumber)->func
        );
    }

    public function assertPinIsOutput(int $pinNumber)
    {
        assertSame(
            Func::OUTPUT,
            $this->fakePins->findByPinNumber($pinNumber)->func
        );
    }

    public function assertPinTurnedOn(int $pinNumber)
    {
        assertSame(
            Level::HIGH,
            $this->fakePins->findByPinNumber($pinNumber)->level
        );
    }

    public function assertPinTurnedOff(int $pinNumber)
    {
        assertSame(
            Level::LOW,
            $this->fakePins->findByPinNumber($pinNumber)->level
        );
    }
}
