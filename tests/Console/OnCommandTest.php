<?php

use DanJohnson95\Pinout\Pinout;

it('throws an error if an invalid pin number is given', function () {
    $this->artisan('pinout:on hello')
        ->expectsOutput('Pin must be a number')
        ->assertExitCode(1);
});

it('turns the pin on', function () {
    Pinout::fake();

    $this->artisan('pinout:on 1')
        ->expectsOutput('Pin 1 is currently HIGH');

    Pinout::assertPinTurnedOn(1);
});
