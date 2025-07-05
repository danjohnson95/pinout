<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;

class SysFileGPIOD implements Commandable
{
    protected ?string $gpioChip;

    public function getAll(array $pinNumbers): PinCollection
    {
        $collection = PinCollection::make();

        foreach ($pinNumbers as $pinNumber) {
            $collection->push($this->get($pinNumber));
        }

        return $collection;
    }

    public function get(int $pinNumber): Pin
    {
        return Pin::make(
            pinNumber: $pinNumber,
            level: $this->getLevel($pinNumber),
            func: Func::INPUT
        );
    }

    protected function getLevel(int $pinNumber): Level
    {
        $chip = $this->gpioChip;
        $level = trim(shell_exec("gpioget $chip $pinNumber"));

        if ($level === "0") {
            return Level::LOW;
        } else {
            return Level::HIGH;
        }
    }

    public function setFunction(int $pinNumber, Func $func): self
    {
        return $this;
    }

    public function exportPin(int $pinNumber): self
    {
        return $this;
    }

    public function setLevel(int $pinNumber, Level $level): self
    {
        $chip = $this->gpioChip;
        $line = $pinNumber;
        $value = $level->value;

        $command = "gpioset $chip $line=$value";
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \Exception("Failed to set GPIO level on line $line. Exit code: $exitCode");
        }

        return $this;
    }



    public function __construct()
    {
        $this->gpioChip = config('pinout.gpio_chip');
    }
}
