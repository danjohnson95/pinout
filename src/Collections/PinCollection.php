<?php

namespace DanJohnson95\Pinout\Collections;

use DanJohnson95\Pinout\Entities\Pin;
use Illuminate\Support\Collection;

class PinCollection extends Collection
{
    public function turnOn(): self
    {
        $this->each->turnOn();

        return $this;
    }

    public function turnOff(): self
    {
        $this->each->turnOff();

        return $this;
    }

    public function findByPinNumber(int $pinNumber): ?Pin
    {
        return $this->firstWhere(
            fn (Pin $pin) => $pin->pinNumber === $pinNumber
        );
    }

    public function whereIsOn(): self
    {
        return $this->filter->isOn();
    }

    public function whereIsOff(): self
    {
        return $this->filter->isOff();
    }
}
