<?php

namespace DanJohnson95\Pinout\Shell;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Exceptions\CommandUnavailable;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;
use Nette\NotImplementedException;

class Gpio implements Commandable
{
    protected const EXECUTABLE = 'gpio';

    protected function handleError(string $errorOutput): void
    {
        if (str_contains($errorOutput, 'command not found')) {
            throw new CommandUnavailable();
        }

        // if (preg_match("/Unknown\sGPIO\s\"(\d+)\"/", $errorOutput, $matches)) {
        //     throw new PinNotFound((int) $matches[1]);
        // }
    }

    protected function run(string $command): ProcessResult
    {
        $result = Process::run(self::EXECUTABLE . " {$command}");

        if (!$result->successful()) {
            $this->handleError($result->output());
        }

        return $result;
    }

    public function getAll(array $pinNumbers): PinCollection
    {
        throw new NotImplementedException();
    }

    public function get(int $pinNumber): Pin
    {
        $process = $this->run("-g read {$pinNumber}");

        return Pin::make(
            pinNumber: $pinNumber,
            level: Level::from($process->output()),
        );
    }

    public function setFunction(int $pinNumber, Func $func): self
    {
        throw new NotImplementedException();
    }

    public function setLevel(int $pinNumber, Level $level): self
    {
        $cmdLevel = match ($level) {
            Level::LOW => '0',
            Level::HIGH => '1',
        };

        $this->run("-g write {$pinNumber} {$cmdLevel}");

        return $this;
    }
}
