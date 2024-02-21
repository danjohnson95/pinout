<?php

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Exceptions\CommandUnavailable;
use DanJohnson95\Pinout\Exceptions\PinNotFound;
use DanJohnson95\Pinout\Facade as Pin;
use Illuminate\Support\Facades\Process;

it('returns pin state for multiple pins', function () {
    Process::fake([
        'raspi-gpio get 1,2,3' => Process::result(
            output: "GPIO 1: level=1 fsel=1 func=OUTPUT\nGPIO 2: level=0 fsel=1 func=INPUT\nGPIO 3: level=1 fsel=1 alt=3 func=OUTPUT",
        ),
    ]);

    $output = Pin::getAll([1, 2, 3]);

    assertCount(3, $output);

    $pin13 = $output->firstWhere('pin', 1);
    assertSame(Level::HIGH, $pin13->level);
    assertSame(1, $pin13->fsel);
    assertSame('OUTPUT', $pin13->output);
    assertSame(null, $pin13->alt);

    $pin14 = $output->firstWhere('pin', 2);
    assertSame(Level::LOW, $pin14->level);
    assertSame(1, $pin13->fsel);
    assertSame('INPUT', $pin13->output);
    assertSame(null, $pin13->alt);

    $pin15 = $output->firstWhere('pin', 3);
    assertSame(Level::HIGH, $pin15->level);
    assertSame(1, $pin15->fsel);
    assertSame('OUTPUT', $pin15->output);
    assertSame(3, $pin15->alt);
});

it('throws exception if one of the pins doesnt exist', function () {
    Process::fake([
        'raspi-gpio get 1,2,69' => Process::result(
            output: 'Error: This pin is not exported',
            exitCode: 1,
        ),
    ]);

    $this->expectException(PinNotFound::class);

    Pin::get(1, 2, 69);
});

it('throws exception if raspi-gpio is not installed', function () {
    Process::fake([
        'raspi-gpio get 1,2,3' => Process::result(
            output: 'Command not found',
            exitCode: 1,
        ),
    ]);

    $this->expectException(CommandUnavailable::class);

    Pin::getAll(1, 2, 3);
});
