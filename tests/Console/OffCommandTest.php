<?php

use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Pinout;

it('throws an error if an invalid pin number is given', function () {
    $this->artisan('pinout:off hello')
        ->expectsOutput('Pin must be a number')
        ->assertExitCode(1);
});

it('turns the pin off', function () {
    Pinout::shouldReceive('pin->turnOff')
        ->andReturn(Pin::make(
            pinNumber: 1,
            level: Level::LOW,
            fsel: 1,
            func: 'output',
            alt: null,
        ));

    $this->artisan('pinout:off 1')
        ->expectsOutput('Pin 1 is currently LOW');
});
