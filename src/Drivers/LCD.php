<?php

namespace DanJohnson956\Pinout\Drivers;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Level;

class LCD
{
    protected Pin $readSelect;
    protected Pin $enable;
    protected Pin $data4;
    protected Pin $data5;
    protected Pin $data6;
    protected Pin $data7;

    public static function make(
        Pin $readSelect,
        Pin $enable,
        Pin $data4,
        Pin $data5,
        Pin $data6,
        Pin $data7,
    ) {
        $lcd = new self();
        $lcd->readSelect = $readSelect;
        $lcd->enable = $enable;
        $lcd->data4 = $data4;
        $lcd->data5 = $data5;
        $lcd->data6 = $data6;
        $lcd->data7 = $data7;

        PinCollection::make([
            $readSelect,
            $enable,
            $data4,
            $data5,
            $data6,
            $data7,
        ])->makeOutput();

        return $lcd;
    }

    public function writeChar(string $char)
    {
        $this->writeData(ord($char));

        return $this;
    }

    protected function dataInputMode(): self
    {
        $this->readSelect->turnOn();

        return $this;
    }

    protected function instructionInputMode(): self
    {
        $this->readSelect->turnOff();

        return $this;
    }

    protected function pulseEnable(): self
    {
        $this->enable->turnOff();
        usleep(1);
        $this->enable->turnOn();
        usleep(1);
        $this->enable->turnOff();
        usleep(100); // commands need >37us to settle

        return $this;
    }

    protected function writeInitNibble(int $data)
    {
        $this->write4Bits($data >> 4);

        return $this;
    }

    protected function writeData(int $data)
    {
        $this->dataInputMode();
        $this->write8Bits($data);

        return $this;
    }

    protected function write8Bits(int $data): self
    {
        $this->write4Bits($data >> 4);
        $this->write4Bits($data);

        return $this;
    }

    protected function write4Bits(int $data)
    {
        $this->data4->setLevel(Level::from($data & 0x08));
        $this->data5->setLevel(Level::from($data & 0x04));
        $this->data6->setLevel(Level::from($data & 0x02));
        $this->data7->setLevel(Level::from($data & 0x01));
        $this->pulseEnable();
    }

    public function clearDisplay()
    {
        $this->instructionInputMode();
        $this->write8Bits(0b00000001);
    }
}
