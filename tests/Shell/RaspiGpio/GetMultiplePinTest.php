<?php

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;

use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Exceptions\CommandUnavailable;
use DanJohnson95\Pinout\Exceptions\PinNotFound;
use DanJohnson95\Pinout\Pinout;
use Illuminate\Support\Facades\Process;

it('returns pins for multiple', function () {
    Process::fake([
        'raspi-gpio get 1,2,3' => Process::result(
            output: "GPIO 1: level=1 fsel=1 func=OUTPUT\nGPIO 2: level=0 fsel=1 func=INPUT\nGPIO 3: level=1 fsel=1 alt=3 func=OUTPUT",
        ),
    ]);

    $output = Pinout::pins(1, 2, 3);

    assertCount(3, $output);

    $pin1 = $output->findByPinNumber(1);
    assertSame(Level::HIGH, $pin1->level);
    assertSame(1, $pin1->fsel);
    assertSame('OUTPUT', $pin1->func);
    assertSame(null, $pin1->alt);

    $pin2 = $output->findByPinNumber(2);
    assertSame(Level::LOW, $pin2->level);
    assertSame(1, $pin2->fsel);
    assertSame('INPUT', $pin2->func);
    assertSame(null, $pin2->alt);

    $pin3 = $output->findByPinNumber(3);
    assertSame(Level::HIGH, $pin3->level);
    assertSame(1, $pin3->fsel);
    assertSame('OUTPUT', $pin3->func);
    assertSame(3, $pin3->alt);
});

it('throws exception if one of the pins doesnt exist', function () {
    Process::fake([
        'raspi-gpio get 1,2,69' => Process::result(
            output: 'Unknown GPIO "69"',
            exitCode: 1,
        ),
    ]);

    $this->expectException(PinNotFound::class);

    Pinout::pins(1, 2, 69);
});

it('throws exception if raspi-gpio is not installed', function () {
    Process::fake([
        'raspi-gpio get 1,2,3' => Process::result(
            output: 'zsh: command not found: raspi-gpio',
            exitCode: 1,
        ),
    ]);

    $this->expectException(CommandUnavailable::class);

    Pinout::pins(1, 2, 3);
});
