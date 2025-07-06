<?php

namespace DanJohnson95\Pinout\Entities;

use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Facades\PinService;

class Pin
{
    public int $pinNumber;
    public Func $func;
    public Level $level;

    public static function make(
        int $pinNumber,
        Level $level,
        Func $func,
    ): self {
        $pin = new self();
        $pin->pinNumber = $pinNumber;
        $pin->level = $level;
        $pin->func = $func;

        app(\DanJohnson95\Pinout\Shell\Commandable::class)->exportPin($pinNumber);

        return $pin;
    }

    private function refresh(): self
    {
        $pin = PinService::pin($this->pinNumber);
        $this->level = $pin->level;
        $this->func = $pin->func;

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

    public function isInput(): bool
    {
        return $this->refresh()->func === Func::INPUT;
    }

    public function isOutput(): bool
    {
        return $this->refresh()->func === Func::OUTPUT;
    }

    public function setLevel(Level $level): self
    {
        return PinService::setLevel($this, $level);
    }

    public function turnOn(): self
    {
        return PinService::setLevel($this, Level::HIGH);
    }

    public function turnOff(): self
    {
        return PinService::setLevel($this, Level::LOW);
    }

    public function makeInput(): self
    {
        return dd(PinService::setFunction($this, Func::INPUT));
    }

    public function makeOutput(): self
    {
        return PinService::setFunction($this, Func::OUTPUT);
    }
}
