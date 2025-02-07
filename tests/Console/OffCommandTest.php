<?php

use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Facades\PinService;

it('throws an error if an invalid pin number is given', function () {
    $this->artisan('pinout:off hello')
        ->expectsOutput('Pin must be a number')
        ->assertExitCode(1);
});

it('throws an error when the pin isn\'t configured for output', function () {
    PinService::fake();

    PinService::shouldReceive('pin')
        ->andReturn(Pin::make(pinNumber: 1, level: Level::LOW, func: Func::INPUT));

    $this->artisan('pinout:off 1')
        ->expectsOutput('Pin 1 isn\'t configured for output')
        ->assertExitCode(1);
});

it('turns the pin off', function () {
    PinService::fake();

    $this->artisan('pinout:off 1')
        ->expectsOutput('Pin 1 is currently LOW');

    PinService::assertPinTurnedOff(1);
});
