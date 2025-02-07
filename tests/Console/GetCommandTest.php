<?php

use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Facades\PinService;

it('throws an error if an invalid pin number is given', function () {
    $this->artisan('pinout:get hello')
        ->expectsOutput('Pin must be a number')
        ->assertExitCode(1);
});

it('returns the status of the given pin', function () {
    PinService::fake();

    PinService::shouldReceive('pin')
        ->with(1)
        ->andReturn(Pin::make(
            pinNumber: 1,
            level: Level::HIGH,
            fsel: 1,
            func: 'output',
            alt: null,
        ));

    $this->artisan('pinout:get 1')
        ->expectsOutput('Pin 1 is currently HIGH');
});
