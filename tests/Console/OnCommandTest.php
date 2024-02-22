<?php

use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Pinout;

it('throws an error if an invalid pin number is given', function () {
    $this->artisan('pinout:on hello')
        ->expectsOutput('Pin must be a number')
        ->assertExitCode(1);
});

it('turns the pin on', function () {
    Pinout::shouldReceive('pin->turnOn')
        ->andReturn(Pin::make(
            pinNumber: 1,
            level: Level::HIGH,
            fsel: 1,
            func: 'output',
            alt: null,
        ));

    $this->artisan('pinout:on 1')
        ->expectsOutput('Pin 1 is currently HIGH');
});
