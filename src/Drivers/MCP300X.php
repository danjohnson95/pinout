<?php

declare(strict_types=1);

namespace DanJohnson95\Pinout\Drivers;

use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\SPIMode;
use DanJohnson95\Pinout\Enums\MCP;
use DanJohnson95\Pinout\Drivers\SPIBus;
use Mockery;

class MCP300X
{
    private int $maxPin = 7;
    private int $mcp3004MaxPin = 3;
    private int $maxVoltageInt = 1023;

    public static function make (
        Pin $chipSelect,
        Pin $clock,
        Pin $miSO,
        Pin $moSI,
        MCP $model,
        float $vcc
    ): self {

        return new self (
            spi: SPIBus::make(
                chipSelect: $chipSelect,
                clock: $clock,
                miSO: $miSO,
                moSI: $moSI,
                mode: SPIMode::MODE0
            ),
            vcc: $vcc,
            model: $model
        );
        
        return (new self(
            $chipSelect,
            $clock,
            $miSO,
            $moSI,
            $mode
        ))->init();
    }

    private function __construct (
        protected mixed $spi,
        protected float $vcc,
        protected MCP $model
    ) {
        //
    }

    public function getAnalogPin(
        int $pinNumber
    ): ?float {

        if ( $pinNumber < 0 ) {
            throw new \Exception("MCP300x error : Negative pins are not a valid pins");
        }

        if ($pinNumber > $this->maxPin) {
            throw new \Exception("MCP300X error : Pin $pinNumber is not a valid pin");
        }

        if (($this->model == MCP::MCP3004) && ($pinNumber > $this->mcp3004MaxPin)) {
            throw new \Exception("MCP300x error : Pin $pinNumber is not a valid pin on MCP3004");
        }

        $this->spi
            ->enableChip()
            ->writeBits(
                sprintf('000000011%03b000000000000', $pinNumber),
                true
            )->disableChip();

        $data = substr($this->spi->readBits, -10);
        $analogLevel = bindec($data);
        return ($this->vcc / $this->maxVoltageInt) * $analogLevel;
    }
}
