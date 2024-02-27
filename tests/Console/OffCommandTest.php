<?php

use DanJohnson95\Pinout\Pinout;

it('throws an error if an invalid pin number is given', function () {
    $this->artisan('pinout:off hello')
        ->expectsOutput('Pin must be a number')
        ->assertExitCode(1);
});

it('turns the pin off', function () {
    Pinout::fake();

    $this->artisan('pinout:off 1')
        ->expectsOutput('Pin 1 is currently LOW');

    Pinout::assertPinTurnedOff(1);
});
