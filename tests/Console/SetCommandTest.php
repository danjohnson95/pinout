<?php

use function PHPUnit\Framework\assertTrue;

use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Facade;
use DanJohnson95\Pinout\Entities\PinState;
use DanJohnson95\Pinout\Enums\Func;

it('throws an error if an invalid pin number is given', function () {
    $this->artisan('pinout:set hello')
        ->expectsOutput('Pin must be a number')
        ->assertExitCode(1);
});

it('sets to output', function () {
    Facade::shouldReceive('setFunction')
        ->with(1, Func::OUTPUT)
        ->once();

    $this->artisan('pinout:set 1 --func=OUTPUT');
});
