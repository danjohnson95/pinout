<?php

declare(strict_types=1);

namespace DanJohnson95\Pinout\Drivers;

use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\SPIMode;
use DanJohnson95\Pinout\Enums\SPIClockStage;
use DanJohnson95\Pinout\Facades\PinService;

class SPIBus
{
    public string $readBits = '';
    public array $readBytes = [];

    public static function make (
        Pin $chipSelect,
        Pin $clock,
        Pin $miSO,
        Pin $moSI,
        SPIMode $mode
    ): self {
        
        return (new self(
            $chipSelect,
            $clock,
            $miSO,
            $moSI,
            $mode
        ))->init();
    }

    private function __construct (
        public Pin $chipSelect,
        public Pin $clock,
        public Pin $miSO,
        public Pin $moSI,
        public SPIMode $mode
    ) {
        //
    }

    public function init(): self
    {
        $this->setClock(SPIClockStage::IDLE);
        $this->moSI->turnOff();
        $this->miSO->makeInput();
        $this->chipSelect->turnOn();
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

        if ($stage == SPIClockStage::IDLE) {
            match($this->mode) {
                SPIMode::MODE0 => $this->clock->turnOff(),
                SPIMode::MODE1 => $this->clock->turnOff(),
                SPIMode::MODE2 => $this->clock->turnOn(),
                SPIMode::MODE3 => $this->clock->turnOn(),
            };
            return $this;
        }

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

    public function writeBytes(
        array $bytes,
        bool $readWhileWriting = false
    ): self {
        $this->writeBits(
            collect($bytes)
                ->map(fn($e) => sprintf('%08b', $e))
                ->implode(''),
            $readWhileWriting
        );
        if ($readWhileWriting) {
            $this->fillBytesFromBits();
        }
        return $this; 
    }
    
    public function writeBits(
        string $bits,
        bool $readWhileWriting = false
    ): self {
        if ($readWhileWriting) {
            $this->readBits = '';
        }
        collect(str_split($bits))->each(function (string $bit) use ($readWhileWriting) {
            $this->setClock(SPIClockStage::READY);
            if ($bit === '1') {
                $this->moSI->turnOn();
            } else {
                $this->moSI->turnOff();
            }
            $this->setClock(SPIClockStage::SAMPLED);
            if ($readWhileWriting) {
                $this->readBits .= $this->miSO->isOn() ? '1' : '0';
            }
        });
        $this->setClock(SPIClockStage::READY);
        return $this;
    }

    public function readBytes(
        int $byteCount
    ): self {
        $this->readBytes = [];
        return $this->readBits($byteCount * 8)
            ->fillBytesFromBits();
    }

    public function fillBytesFromBits(): self
    {
        $this->readBytes = collect(str_split($this->readBits, 8))
            ->map(fn($e) => bindec($e))
            ->toArray();
        return $this;
    }

    public function readBits(
        int $bitCount
    ): self {
        $this->readBits = '';
        for ($i = 0; $i < $bitCount; $i++) {
            $this->setClock(SPIClockStage::READY);
            $this->setClock(SPIClockStage::SAMPLED);
            $this->readBits .= $this->miSO->isOn()? '1' : '0';
        }

        $this->setClock(SPIClockStage::READY);
        return $this;
    }
}
