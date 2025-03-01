<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;

class SysFile implements Commandable
{
    protected array $exportedPins = [];
    protected string $baseDirectory = "/sys/class/gpio";

    /**
     * @return array<Pin>
     */
    public function getExportedPins(): array
    {
        $exportedPins = [];
        $exportedPinsFile = fopen("/sys/class/gpio/export", "r");

        while (($line = fgets($exportedPinsFile)) !== false) {
            $exportedPins[] = (int) $line;
        }

        fclose($exportedPinsFile);

        return $exportedPins;
    }

    protected function pinIsExported(int $pinNumber): bool
    {
        return in_array($pinNumber, $this->exportedPins);
    }

    public function exportPin(int $pinNumber): void
    {
        shell_exec('gpio export ' . $pinNumber . ' output');

        $this->exportedPins[] = $pinNumber;
    }

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
        if (! $this->pinIsExported($pinNumber)) {
            $this->exportPin($pinNumber);
        }

        return Pin::make(
            pinNumber: $pinNumber,
            level: $this->getLevel($pinNumber),
            func: $this->getFunction($pinNumber)
        );
    }

    protected function getFunction(int $pinNumber): Func
    {
        $functionFile = fopen("{$this->baseDirectory}/gpio{$pinNumber}/direction", "r");
        $function = fread($functionFile, 3);
        fclose($functionFile);

        if ($function === "in") {
            return Func::INPUT;
        } else {
            return Func::OUTPUT;
        }
    }

    protected function getLevel(int $pinNumber): Level
    {
        $levelFile = fopen("{$this->baseDirectory}/gpio{$pinNumber}/value", "r");
        $level = fread($levelFile, 1);
        fclose($levelFile);

        if ($level === "0") {
            return Level::LOW;
        } else {
            return Level::HIGH;
        }
    }

    public function setFunction(int $pinNumber, Func $func): self
    {
        if (! $this->pinIsExported($pinNumber)) {
            $this->exportPin($pinNumber);
        }

        // Now that the pin is exported, we can set its function
        $functionFile = fopen("{$this->baseDirectory}/gpio{$pinNumber}/direction", "w");

        if ($func === Func::INPUT) {
            $func = "in";
        } else {
            $func = "out";
        }

        fwrite($functionFile, $func);

        return $this;
    }

    public function setLevel(int $pinNumber, Level $level): self
    {
        if (! $this->pinIsExported($pinNumber)) {
            $this->exportPin($pinNumber);
        }

        // Now that the pin is exported, we can set its level
        $levelFile = fopen("{$this->baseDirectory}/gpio{$pinNumber}/value", "w");
        fwrite($levelFile, $level->value);

        return $this;
    }
}
