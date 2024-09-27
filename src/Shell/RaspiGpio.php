<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Exceptions\CantSetPinToFunction;
use DanJohnson95\Pinout\Exceptions\CommandUnavailable;
use DanJohnson95\Pinout\Exceptions\PinNotFound;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

class RaspiGpio implements Commandable
{
    protected const EXECUTABLE = 'raspi-gpio';

    protected function handleError(string $errorOutput): void
    {
        if (str_contains($errorOutput, 'command not found')) {
            throw new CommandUnavailable();
        }

        if (preg_match("/Unknown\sGPIO\s\"(\d+)\"/", $errorOutput, $matches)) {
            throw new PinNotFound((int) $matches[1]);
        }
    }

    protected function run(string $command): ProcessResult
    {
        $result = Process::run(self::EXECUTABLE . " {$command}");

        if (!$result->successful()) {
            $this->handleError($result->output());
        }

        return $result;
    }

    protected static function parsePinState(string $state): Pin
    {
        $matches = [];

        [$prefix, $options] = explode(':', $state);

        preg_match('/GPIO\s(\d+)/', $prefix, $matches);
        [, $pin] = $matches;

        $options = parse_ini_string(str_replace(' ', PHP_EOL, $options));

        return Pin::make(
            pinNumber: (int) $pin,
            level: Level::from($options['level'] ?? 0),
            fsel: isset($options['fsel']) ? (int) $options['fsel'] : null,
            func: $options['func'] ?? null,
            alt: $options['alt'] ?? null,
        );
    }

    public function getAll(array $pinNumbers): PinCollection
    {
        $pins = implode(",", $pinNumbers);

        $process = $this->run("get {$pins}");
        $lines = explode("\n", trim($process->output()));

        $pinStates = PinCollection::make();

        foreach ($lines as $line) {
            $state = self::parsePinState($line);
            $pinStates->push($state);
        }

        return $pinStates;
    }

    public function get(int $pinNumber): Pin
    {
        $process = $this->run("get {$pinNumber}");

        return self::parsePinState($process->output());
    }

    public function setFunction(int $pinNumber, Func $func): self
    {
        $cmdFunc = match ($func) {
            Func::INPUT => 'ip',
            Func::OUTPUT => 'op',
            default => throw new CantSetPinToFunction($pinNumber, $func),
        };

        $this->run("set {$pinNumber} {$cmdFunc}");

        return $this;
    }

    public function setLevel(int $pinNumber, Level $level): self
    {
        $cmdLevel = match ($level) {
            Level::LOW => 'dl',
            Level::HIGH => 'dh',
        };

        $this->run("set {$pinNumber} {$cmdLevel}");

        return $this;
    }
}