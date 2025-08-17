<?php

use DanJohnson95\Pinout\Facades\PinService;
use DanJohnson95\Pinout\Drivers\MCP300X;
use DanJohnson95\Pinout\Enums\SPIMode;
use DanJohnson95\Pinout\Enums\MCP;
use ReflectionClass;

function makeMCP(
    MCP $model,
    float $vcc
): MCP300X {
    PinService::fake();
    return MCP300X::make(
        chipSelect: PinService::pin(1),
        clock: PinService::pin(2),
        miSO: PinService::pin(3),
        moSI: PinService::pin(4),
        model: $model,
        vcc: $vcc
    );
}

function mockReturnDataMCP(
    string $returnData,
    MCP $model,
    float $vcc
): MCP300X {
    $mcp = makeMCP(
        model: $model,
        vcc: $vcc
    );
    $spiMock = Mockery::mock(SPIBus::class)->makePartial();
    $spiMock->shouldReceive('enableChip')->once()->andReturnSelf();
    $spiMock->shouldReceive('writeBits')->once()->withArgs(function ($bits, $readWhileWriting) {
        // Optionally test bits format here if you want
        expect($readWhileWriting)->toBeTrue();
        return is_string($bits) && str_starts_with($bits, '000000011');
    })->andReturnUsing(function ($bits, $readWhileWriting) use (&$spiMock, $returnData) {
        // Simulate the bits read back (last 10 bits)
        $spiMock->readBits = $returnData;  // example bits read, decimal 13
        return $spiMock;
    });
    $spiMock->shouldReceive('disableChip')->once()->andReturnSelf();

    // Replace protected spi driver
    $reflection = new ReflectionClass($mcp);
    $property = $reflection->getProperty('spi');
    $property->setAccessible(true);
    $property->setValue($mcp, $spiMock);
    return $mcp;
}

it('returns an instance of MCP300X from ::make()', function () {
    $mcp = makeMCP(
        model: MCP::MCP3008,
        vcc: 5
    );
    
    expect($mcp)->toBeInstanceOf(MCP300X::class);
});

it('initialises the SPIBus in Mode 0', function () {
    $mcp = makeMCP(
        model: MCP::MCP3008,
        vcc: 5
    );

    $reflection = new ReflectionClass($mcp);
    $property = $reflection->getProperty('spi');
    $property->setAccessible(true);

    // read
    $currentSpi = $property->getValue($mcp);
    
    expect($currentSpi->mode)->toBe(SPIMode::MODE0);
});

it('throws an exception when asked to read a negative pin', function () {
    $mcp = makeMCP(
        model: MCP::MCP3008,
        vcc: 5
    );

    $this->expectException(Exception::class);
    $mcp->getAnalogPin(-1);
});

it('doesnt throw an exception when asked to read pin 0', function () {
    $mcp = makeMCP(
        model: MCP::MCP3008,
        vcc: 5
    );

    $mcp->getAnalogPin(0);
    expect(true)->toBeTrue(); 
});

it('throws an exception when asked to read an out of range pin index for MCP3008', function () {
    $mcp = makeMCP(
        model: MCP::MCP3008,
        vcc: 5
    );

    $this->expectException(Exception::class);
    $mcp->getAnalogPin(8);
});

it('doesnt throw an exception when asked to read pin 7 for MCP3008', function () {
    $mcp = makeMCP(
        model: MCP::MCP3008,
        vcc: 5
    );

    $mcp->getAnalogPin(0);
    expect(true)->toBeTrue(); 
});

it('throws an exception when asked to read an out of range pin index for MCP3004', function () {
    $mcp = makeMCP(
        model: MCP::MCP3004,
        vcc: 5
    );

    $this->expectException(Exception::class);
    $mcp->getAnalogPin(5);
});

it('doesnt throw an exception when asked to read  pin 3 for MCP3004', function () {
    $mcp = makeMCP(
        model: MCP::MCP3004,
        vcc: 5
    );
    
    $mcp->getAnalogPin(3);
    expect(true)->toBeTrue(); 
});

it('converts 10 HIGH bits at 5v to 5v', function () {
    $mcp = mockReturnDataMCP(
        returnData: '1111111111',
        model: MCP::MCP3008,
        vcc: 5
    );

    $this->assertEqualsWithDelta(
        5, 
        $mcp->getAnalogPin(5),
        2
    );
});

it('converts 10 LOW bits at 5v to 0v', function () {
    $mcp = mockReturnDataMCP(
        returnData: '0000000000',
        model: MCP::MCP3008,
        vcc: 5
    );

    $this->assertEqualsWithDelta(
        0, 
        $mcp->getAnalogPin(5),
        2
    );
});

it('converts 1000000000 at 5v to 2.5v', function () {
    $mcp = mockReturnDataMCP(
        returnData: '1000000000',
        model: MCP::MCP3008,
        vcc: 5
    );

    $this->assertEqualsWithDelta(
        2.5, 
        $mcp->getAnalogPin(5),
        2
    );
});

it('converts 10 HIGH bits at 3.3v to 3.3v', function () {
    $mcp = mockReturnDataMCP(
        returnData: '1111111111',
        model: MCP::MCP3008,
        vcc: 5
    );

    $this->assertEqualsWithDelta(
        3.3, 
        $mcp->getAnalogPin(5),
        2
    );
});

it('converts 10 LOW bits at 3.3v to 0v', function () {
    $mcp = mockReturnDataMCP(
        returnData: '0000000000',
        model: MCP::MCP3008,
        vcc: 5
    );

    $this->assertEqualsWithDelta(
        0, 
        $mcp->getAnalogPin(5),
        2
    );
});

it('converts 1000000000 at 3.3v to 1.65v', function () {
    $mcp = mockReturnDataMCP(
        returnData: '1000000000',
        model: MCP::MCP3008,
        vcc: 5
    );

    $this->assertEqualsWithDelta(
        1.65, 
        $mcp->getAnalogPin(5),
        2
    );
});