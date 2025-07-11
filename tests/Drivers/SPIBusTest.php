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

it('calls writeBits with the correct bit stream when passed bytes', function () {
    $bytes = [65, 66]; // ASCII 'A' and 'B' -> 01000001 01000010
    $expectedBits = '0100000101000010';

    // Create a partial mock
    $mock = Mockery::mock(SPIBus::class)->makePartial();

    // Expect writeBits to be called with the correct bit stream
    $mock->shouldReceive('writeBits')
        ->once()
        ->with($expectedBits, false);

    // Call the real method writeBytes
    $mock->writeBytes($bytes);
});

it('doesnt fillBytesFromBits when not reading during write', function () {
    $bytes = [65, 66]; // ASCII 'A' and 'B' -> 01000001 01000010
    $expectedBits = '0100000101000010';

    // Create a partial mock
    $mock = Mockery::mock(SPIBus::class)->makePartial();

    // Expect writeBits to be called with the correct bit stream
    $mock->shouldReceive('writeBits')
        ->once()
        ->with($expectedBits, false);

    // Optionally mock fillBytesFromBits if readWhileWriting is true
    $mock->shouldNotReceive('fillBytesFromBits');

    // Call the real method writeBytes
    $mock->writeBytes($bytes);
});

it('does fillBytesFromBits when reading during write', function () {
    $bytes = [65, 66]; // ASCII 'A' and 'B' -> 01000001 01000010
    $expectedBits = '0100000101000010';

    // Create a partial mock
    $mock = Mockery::mock(SPIBus::class)->makePartial();

    // Expect writeBits to be called with the correct bit stream
    $mock->shouldReceive('writeBits')
        ->once()
        ->with($expectedBits, true);

    $mock->shouldReceive('setClock');

    // Optionally mock fillBytesFromBits if readWhileWriting is true
    $mock->shouldReceive('fillBytesFromBits');

    PinService::fake();
    $mock->chipSelect = PinService::pin(1);
    $mock->clock = PinService::pin(2);
    $mock->miSO = PinService::pin(3);
    $mock->moSI = PinService::pin(4);
    $mock->mode = SPIMode::MODE0;

    // Call the real method writeBytes
    $mock->writeBytes($bytes,true);
});

it('converts bits to bytes after readingWhileWriting', function () {
    $bytes = [65, 66]; // ASCII 'A' and 'B' -> 01000001 01000010
    $expectedBits = '0100000101000010';

    // Create a partial mock
    $mock = Mockery::mock(SPIBus::class)->makePartial();

    // Expect writeBits to be called with the correct bit stream
    $mock->shouldReceive('writeBits')
        ->once()
        ->with($expectedBits, true)
        ->andReturnUsing(function ($bits, $readWhileWriting) use ($mock) {
            // Simulate filling readBits with mock data
            $mock->readBits = '0100001001000001'; // Example response
            return $mock;
        });

    $mock->shouldReceive('setClock');

    PinService::fake();
    $mock->chipSelect = PinService::pin(1);
    $mock->clock = PinService::pin(2);
    $mock->miSO = PinService::pin(3);
    $mock->moSI = PinService::pin(4);
    $mock->mode = SPIMode::MODE0;

    // Call the real method writeBytes
    $mock->writeBytes($bytes, true);

    expect($mock->readBytes)->toBe([66, 65]);
});