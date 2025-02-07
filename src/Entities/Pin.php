<?php

namespace DanJohnson95\Pinout\Entities;

use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Pinout;

class Pin
{
    public int $pinNumber;
    public Level $level;

    public static function make(
        int $pinNumber,
        Level $level,
    ): self {
        $pin = new self();
        $pin->pinNumber = $pinNumber;
        $pin->level = $level;

        return $pin;
    }

    private function refresh(): self
    {
        $pin = Pinout::pin($this->pinNumber);
        $this->level = $pin->level;

        return $this;
    }

    public function isOn(): bool
    {
        return $this->refresh()->level === Level::HIGH;
    }

    public function isOff(): bool
    {
        return $this->refresh()->level === Level::LOW;
    }

    public function setLevel(Level $level): self
    {
        return Pinout::setLevel($this, $level);
    }

    public function turnOn(): self
    {
        return Pinout::setLevel($this, Level::HIGH);
    }

    public function turnOff(): self
    {
        return Pinout::setLevel($this, Level::LOW);
    }

    public function makeInput(): self
    {
        return Pinout::setFunction($this, Func::INPUT);
    }

    public function makeOutput(): self
    {
        return Pinout::setFunction($this, Func::OUTPUT);
    }
}
