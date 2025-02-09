<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use Exception;

class SysFile implements Commandable
{
    protected array $exportedPins = [];

    protected array $pinValues = [];
    protected array $pinDirections = [];

    public function __construct()
    {
        $this->exportedPins = [];//$this->getExportedPins();
    }

    public function getExportedPins()
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
        // $exportedPinsFile = fopen("/sys/class/gpio/export", "w");
        // fwrite($exportedPinsFile, $pinNumber);
        shell_exec('gpio export ' . $pinNumber . ' output');

        $this->exportedPins[] = $pinNumber;
        // fclose($exportedPinsFile);
    }

    public function getAll(array $pinNumbers): PinCollection
    {
        throw new Exception("Not implemented");
    }

    public function get(int $pinNumber): Pin
    {
        if (! $this->pinIsExported($pinNumber)) {
            $this->exportPin($pinNumber);
        }

        return Pin::make(
            pinNumber: (int) $pinNumber,
            level: Level::from($this->getLevel($pinNumber)),
            func: $this->getFunction($pinNumber)
        );
    }

    protected function getFunction(int $pinNumber)
    {
        $functionFile = fopen("/sys/class/gpio/gpio{$pinNumber}/direction", "r");
        $function = fread($functionFile, 3);
        fclose($functionFile);
        return $function;
    }

    protected function getLevel(int $pinNumber)
    {
        return 0;
        $levelFile = fopen("/sys/class/gpio/gpio{$pinNumber}/value", "r");
        $level = fread($levelFile, 1);
        fclose($levelFile);
        return $level;
    }

    public function setFunction(int $pinNumber, Func $func): self
    {
        if (! $this->pinIsExported($pinNumber)) {
            $this->exportPin($pinNumber);
        }

        // Now that the pin is exported, we can set its function
        $functionFile = fopen("/sys/class/gpio/gpio{$pinNumber}/direction", "w");

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
            dump('re-export pin!');
            $this->exportPin($pinNumber);
        }

        if (!empty($this->pinValues[$pinNumber])) {
            $levelFile = $this->pinValues[$pinNumber];
        } else {
            dump('open file for pin ' . $pinNumber);
            $this->pinValues[$pinNumber] = fopen("/sys/class/gpio/gpio{$pinNumber}/value", "w");
            $levelFile = $this->pinValues[$pinNumber];
        }
        // $levelFile = $this->pinValues[$pinNumber] ?? $this->pinValues[$pinNumber] = fopen("/sys/class/gpio/gpio{$pinNumber}/value", "w");

        // Now that the pin is exported, we can set its level
        // dump('set ' . $pinNumber . ' to ' . $level->value);
        usleep(100);

        fwrite($levelFile, $level->value);

        usleep(100);

        return $this;
    }
}
