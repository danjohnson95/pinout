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
            func: $this->getFunction($pinNumber)
        );
    }

    protected function getFunction(int $pinNumber): Func
    {
        $chip = $this->gpioChip; // adjust if needed
        $line = $pinNumber;

        $output = shell_exec("gpioinfo $chip | grep -E '^\\s*line\\s+$line:'");

        if (!$output) {
            throw new \Exception("Could not find GPIO line $line on $chip");
        }

        if (preg_match('/\b(input|output)\b/', $output, $matches)) {
            $direction = $matches[1];

            return match ($direction) {
                'input' => Func::INPUT,
                'output' => Func::OUTPUT,
                default => throw new \Exception("Unknown direction '$direction'"),
            };
        }

        throw new \Exception("Unable to determine direction for GPIO line $line");
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
        $chip = $this->gpioChip;
        $line = $pinNumber;

        if ($func === Func::INPUT) {
            $result = shell_exec("gpiod configure $chip $line input");
        } else {
            $result = shell_exec("gpioset $chip $line=0");
        }

        if ($result === null) {
            throw new \Exception("Failed to set direction for GPIO line $line on $chip");
        }
        return $this;
    }


    public function setLevel(int $pinNumber, Level $level): self
    {
        $chip = $this->gpioChip;
        $line = $pinNumber;
        $value = $level->value;

        $result = shell_exec("gpioset $chip $line=$value");

        if ($result === null) {
            throw new \Exception("Failed to set GPIO level on line $line");
        }

        return $this;
    }


    public function __construct()
    {
        $this->gpioChip = config('pinout.gpio_chip');
    }
}
