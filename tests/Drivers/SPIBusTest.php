<?php

use DanJohnson95\Pinout\Facades\PinService;
use DanJohnson95\Pinout\Drivers\SPIBus;
use DanJohnson95\Pinout\Enums\SPIMode;

$modes = collect([
    'MODE0',
    'MODE1',
    'MODE2',
    'MODE3',
]);

it('can make an instance', function () {
    PinService::fake();

    $spi = SPIBus::make(
        chipSelect: PinService::pin(1),
        clock: PinService::pin(2),
        miSO: PinService::pin(3),
        moSI: PinService::pin(4),
        mode: SPIMode::MODE0
    );

    expect($spi)->toBeInstanceOf(SPIBus::class);
});

it('initialises data and chip select pins', function () {
    PinService::fake();

    $spi = SPIBus::make(
        chipSelect: PinService::pin(1),
        clock: PinService::pin(2),
        miSO: PinService::pin(3),
        moSI: PinService::pin(4),
        mode: SPIMode::MODE0
    );

    PinService::assertPinTurnedOn(1);
    PinService::assertPinIsInput(3);
    PinService::assertPinTurnedOff(4);

    expect($spi)->toBeInstanceOf(SPIBus::class);
});

$modes->each(function ($testMode) {
    it('initialises clock pin correctly for mode ' . $testMode, function () use ($testMode) {
        PinService::fake();
    
        $spi = SPIBus::make(
            chipSelect: PinService::pin(1),
            clock: PinService::pin(2),
            miSO: PinService::pin(3),
            moSI: PinService::pin(4),
            mode: SPIMode::{$testMode}
        );
    
        PinService::assertPinTurnedOn(1);

        match($testMode) {
            'MODE0' => PinService::assertPinTurnedOff(2),
            'MODE1' => PinService::assertPinTurnedOff(2),
            'MODE2' => PinService::assertPinTurnedOn(2),
            'MODE3' => PinService::assertPinTurnedOn(2),
        };
        
        PinService::assertPinIsInput(3);
        PinService::assertPinTurnedOff(4);
    
        expect($spi)->toBeInstanceOf(SPIBus::class);
    });
});

