<?php

declare(strict_types=1);

namespace DanJohnson95\Pinout\Drivers;

use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\SPIMode;
use DanJohnson95\Pinout\Enums\SPIClockStage;

class SPIBus
{
    public string $readBits = '';

    public static function make (
        Pin $chipSelect,
        Pin $clock,
        Pin $dataIn,
        Pin $dataOut,
        SPIMode $mode
    ): self {
        
        return (new self(
            $chipSelect,
            $clock,
            $dataIn,
            $dataOut,
            $mode
        ))->init();
    }

    private function __construct (
        protected Pin $chipSelect,
        protected Pin $clock,
        protected Pin $dataIn,
        protected Pin $dataOut,
        protected SPIMode $mode
    ) {
        //
    }

    public function init(): self
    {
        $this->clock->turnOff();
        $this->dataIn->turnOff();
        $this->dataOut->turnOff();
        $this->disableChip();
        return $this;
    }

    public function enableChip(): self
    {
        // Ensure that the chip is clear
        $this->disableChip();

        // Set the state ready to init
        $this->setClock(SPIClockStage::READY);
        
        $this->chipSelect->turnOff();
        return $this;
    }

    public function setClock(
        SPIClockStage $stage
    ): self {
        // Intentional fallthrough 
        switch($this->mode) {
            // Falling edge sampled modes
            case SPIMode::MODE1 :
            case SPIMode::MODE2 :
                if ($stage == SPIClockStage::READY) {
                    $this->clock->turnOn();
                    break;
                }
                $this->clock->turnOff();
                break;
        
            // Rising edge sampled modes
            case SPIMode::MODE0 :
            case SPIMode::MODE3 :
                if ($stage == SPIClockStage::READY) {
                    $this->clock->turnOff();
                    break;
                }
                $this->clock->turnOn();
                break;
        }

        return $this;
    }

    public function disableChip(): self
    {
        $this->chipSelect->turnOn();
        return $this;
    }
    
    public function writeBits(
        string $bits
    ): self {
        collect(str_split($bits))->each(function (string $bit) {
            $this->setClock(SPIClockStage::READY);
            if ($bit) {
                $this->dataOut->turnOn();
            } else {
                $this->dataOut->turnOff();
            }
            $this->setClock(SPIClockStage::SAMPLED);
        });
        $this->setClock(SPIClockStage::READY);
        return $this;
    }

    public function readBits(
        int $bitCount
    ): self {
        $this->readBits = '';
        for ($i = 0; $i < $bitCount; $i++) {
            $this->setClock(SPIClockStage::READY);
            $this->setClock(SPIClockStage::SAMPLED);
            $this->readBits .= $this->dataIn->isOn()? '1' : '0';
        }

        $this->setClock(SPIClockStage::READY);
        return $this;
    }
}
