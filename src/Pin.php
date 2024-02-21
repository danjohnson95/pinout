<?php

namespace DanJohnson95\Pinout;

use DanJohnson95\Pinout\Entities\PinState;
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
}
