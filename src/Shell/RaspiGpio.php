<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinStateCollection;
use DanJohnson95\Pinout\Entities\PinState;
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

    protected function run(string $command): ProcessResult
    {
        $result = Process::run(self::EXECUTABLE . " {$command}");

        if (!$result->successful() && str_contains($result->errorOutput(), 'command not found')) {
            throw new CommandUnavailable();
        }

        return $result;
    }

    protected static function parsePinState(string $state): PinState
    {
        $matches = [];

        preg_match(
            '/GPIO\s(\d+):\slevel=(1|0)\sfsel=(\d)\sa?l?t?=?(\d)?\s?func=(\w+)/',
            $state,
            $matches
        );

        [, $pin, $level, $fsel, $alt, $func] = $matches;

        return new PinState(
            pin: (int) $pin,
            level: Level::from($level),
            fsel: (int) $fsel,
            func: $func,
            alt: $alt ? (int) $alt : null,
        );
    }

    public function getAll(?array $pinNumbers): PinStateCollection
    {
        $pins = implode(",", $pinNumbers);

        $process = $this->run("get {$pins}");
        $lines = explode("\n", $process->output());

        if (count($lines) !== count($pinNumbers)) {
            throw new PinNotFound(1);
        }

        $pinStates = new PinStateCollection();

        foreach ($lines as $line) {
            $state = self::parsePinState($line);
            $pinStates->push($state);
        }

        return $pinStates;
    }

    public function get(int $pinNumber): PinState
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
            Level::LOW => 'dh',
            Level::HIGH => 'dl',
        };

        $this->run("set {$pinNumber} {$cmdLevel}");

        return $this;
    }
}
