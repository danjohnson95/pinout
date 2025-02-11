<?php

namespace DanJohnson95\Pinout\Services;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Contracts\ManagesPins;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Shell\Commandable;

class PinService implements ManagesPins
{
    public $_onBoot;
    public array $_definedPins;

    public function __construct(protected Commandable $commandTool)
    {
    }

    public function pin(int $pinNumber): Pin
    {
        return $this->commandTool->get($pinNumber);
    }

    public function pins(int ...$pinNumbers): PinCollection
    {
        return $this->commandTool->getAll($pinNumbers);
    }

    public function setLevel(Pin $pin, Level $level): Pin
    {
        $this->commandTool->setLevel($pin->pinNumber, $level);

        return $pin;
    }

    public function setFunction(Pin $pin, Func $func): Pin
    {
        $this->commandTool->setFunction($pin->pinNumber, $func);

        return $pin;
    }

    public function doBoot($loop)
    {
        ($this->_onBoot)($loop);
    }

    public function onBoot(callable $func)
    {
        $this->_onBoot = $func;
    }

    public function definePins(array $pins)
    {
        $this->_definedPins = $pins;
    }

    public function getDefinedPins(): array
    {
        return $this->_definedPins;
    }
}
