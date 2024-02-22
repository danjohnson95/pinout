<?php

namespace DanJohnson95\Pinout\Entities;

use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Pinout;

class Pin
{
    public int $pinNumber;
    public Level $level;
    public ?int $fsel;
    public ?string $func;
    public ?int $alt;

    public static function make(
        int $pinNumber,
        Level $level,
        ?int $fsel = null,
        ?string $func = null,
        ?int $alt = null,
    ): self {
        $pin = new self();
        $pin->pinNumber = $pinNumber;
        $pin->level = $level;
        $pin->fsel = $fsel;
        $pin->func = $func;
        $pin->alt = $alt;

        return $pin;
    }

    private function refresh(): self
    {
        $pin = Pinout::pin($this->pinNumber);
        $this->level = $pin->level;
        $this->fsel = $pin->fsel;
        $this->func = $pin->func;
        $this->alt = $pin->alt;

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
        Pinout::setLevel($this, $level);

        $this->refresh();

        return $this;
    }

    public function turnOn(): self
    {
        Pinout::setLevel($this, Level::HIGH);
        $this->refresh();

        return $this;
    }

    public function turnOff(): self
    {
        Pinout::setLevel($this, Level::LOW);
        $this->refresh();

        return $this;
    }

    public function makeInput(): self
    {
        Pinout::setFunction($this, Func::INPUT);
        $this->refresh();

        return $this;
    }

    public function makeOutput(): self
    {
        Pinout::setFunction($this, Func::OUTPUT);
        $this->refresh();

        return $this;
    }
}
