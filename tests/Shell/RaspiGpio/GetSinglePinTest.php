<?php

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Exceptions\CommandUnavailable;
use DanJohnson95\Pinout\Exceptions\PinNotFound;
use DanJohnson95\Pinout\Facade as Pin;
use Illuminate\Support\Facades\Process;

it('returns pin state for given pin', function () {
    Process::fake([
        'raspi-gpio get 13' => Process::result(
            output: 'GPIO 13: level=1 fsel=1 func=OUTPUT',
        ),
    ]);

    $output = Pin::get(13);

    assertSame(13, $output->pin);
    assertSame(Level::HIGH, $output->level);
    assertSame(1, $output->fsel);
    assertSame('OUTPUT', $output->func);
});

it('sets alt to null when not defined', function () {
    Process::fake([
        'raspi-gpio get 13' => Process::result(
            output: 'GPIO 13: level=1 fsel=1 func=OUTPUT',
        ),
    ]);

    $output = Pin::get(13);

    assertNull($output->alt);
});

it('sets alt correctly when defined', function () {
    Process::fake([
        'raspi-gpio get 13' => Process::result(
            output: 'GPIO 13: level=1 fsel=1 alt=7 func=OUTPUT',
        ),
    ]);

    $output = Pin::get(13);

    assertSame(7, $output->alt);
});

it('throws exception if pin doesnt exist', function () {
    Process::fake([
        'raspi-gpio get 69' => Process::result(
            output: 'Error: This pin is not exported',
            exitCode: 1,
        ),
    ]);

    $this->expectException(PinNotFound::class);

    Pin::get(69);
});

it('throws exception if raspi-gpio is not installed', function () {
    Process::fake([
        'raspi-gpio get 1' => Process::result(
            output: 'Command not found',
            exitCode: 1,
        ),
    ]);

    $this->expectException(CommandUnavailable::class);

    Pin::get(1);
});
