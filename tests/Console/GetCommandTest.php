<?php

use function PHPUnit\Framework\assertTrue;

use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Facade;
use DanJohnson95\Pinout\Entities\PinState;

it('throws an error if an invalid pin number is given', function () {
    $this->artisan('pinout:get hello')
        ->expectsOutput('Pin must be a number')
        ->assertExitCode(1);
});

it('returns the status of the given pin', function () {
    $examplePinState = new PinState(
        pin: 1,
        level: Level::HIGH,
        fsel: 1,
        func: 'output',
        alt: null,
    );

    Facade::shouldReceive('get')
        ->with(1)
        ->andReturn($examplePinState);

    $this->artisan('pinout:get 1')
        ->expectsOutput('Pin 1 is currently HIGH');
});
