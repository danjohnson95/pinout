<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use Illuminate\Support\Facades\Session;

class SysFileGPIOD implements Commandable
{
    protected ?string $gpioChip;

    public function __construct()
    {
        $this->gpioChip = config('pinout.gpio_chip');
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
        return Pin::make(
            pinNumber: $pinNumber,
            level: $this->getLevel($pinNumber),
            func: $this->getFunction($pinNumber)
        );
    }

    protected function getLevel(int $pinNumber): Level
    {
        $chip = $this->gpioChip;

        // Check if direction is output
        $gpioinfo = shell_exec("gpioinfo $chip | grep -E '^\\s*line\\s+$pinNumber:'");

        if (str_contains($gpioinfo, 'output')) {
            // Check flash session for cached output level
            $cached = Session::get("gpio.output.$pinNumber");

            if ($cached !== null) {
                return Level::from($cached);
            }

            // If output but no cached value, assume unknown state
            throw new \Exception("Cannot determine level of output line $pinNumber — no cached value.");
        }

        // Input — safe to read
        $level = trim(shell_exec("gpioget $chip $pinNumber"));

        return match ($level) {
            "0" => Level::LOW,
            "1" => Level::HIGH,
            default => throw new \Exception("Unknown GPIO level '$level' on line $pinNumber"),
        };
    }

    public function getFunction(int $pinNumber): Func
    {
        $chip = $this->gpioChip;

        $gpioinfo = shell_exec("gpioinfo $chip | grep -E '^\\s*line\\s+$pinNumber:'");

        if (str_contains($gpioinfo, 'output')) {
            return Func::OUTPUT;
        }

        return Func::INPUT;
    }

    public function setFunction(int $pinNumber, Func $func): self
    {
        // Optional: Could configure input/output explicitly here with `gpiod configure` if supported
        return $this;
    }

    public function exportPin(int $pinNumber): self
    {
        // No-op for libgpiod
        return $this;
    }

    public function setLevel(int $pinNumber, Level $level): self
    {
        $chip = $this->gpioChip;
        $value = $level->value;

        $command = "gpioset $chip $pinNumber=$value";
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \Exception("Failed to set GPIO level on line $pinNumber. Exit code: $exitCode");
        }

        // Flash level into session
        Session::flash("gpio.output.$pinNumber", $value);

        return $this;
    }
}
