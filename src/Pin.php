<?php

namespace DanJohnson95\Pinout;

use DanJohnson95\Pinout\Entities\PinState;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Shell\Commandable;

class Pin
{
    public function __construct(protected Commandable $commandTool)
    {
    }

    public function getAll(array $pinNumbers)
    {
        return $this->commandTool->getAll($pinNumbers);
    }

    public function get(int $pin): PinState
    {
        return $this->commandTool->get($pin);
    }

    public function setAsInput(int $pin): PinState
    {
        return $this->commandTool->setFunction($pin, Func::INPUT);
    }

    public function setAsOutput(int $pin): PinState
    {
        return $this->commandTool->setFunction($pin, Func::OUTPUT);
    }

    public function setLevel(int $pin, Level $level): PinState
    {
        return $this->commandTool->setLevel($pin, $level);
    }

    public function driveHigh(int $pin): PinState
    {
        return $this->commandTool->setLevel($pin, Level::HIGH);
    }

    public function driveLow(int $pin): PinState
    {
        return $this->commandTool->setLevel($pin, Level::LOW);
    }
}
