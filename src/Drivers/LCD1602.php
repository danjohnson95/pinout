<?php

namespace DanJohnson95\Pinout\Drivers;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Level;

class LCD1602
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
        ])->makeOutput()->turnOff();

        $lcd->initialise()->enableDisplay()->clearDisplay();

        return $lcd;
    }

    public function initialise(): self
    {
        // LCD needs 40 ms to start up after power on
        usleep(40e3);

        // Now put it in 4 bit mode, 2 lines, 5x8 characters
        $this->instructionInputMode();
        usleep(45e3); // 4.5 ms
        $this->write8Bits(0x33);
        usleep(45e3); // 4.5 ms
        $this->write8Bits(0x32);
        usleep(150e3); // 150 us
        $this->write8Bits(0x28);

        usleep(1523e3);

        return $this;
    }

    public function enableDisplay(): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x0E);

        return $this;
    }

    public function invertDisplay(): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x0D);

        return $this;
    }

    public function showCursor(): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x0E);

        return $this;
    }

    public function hideCursor(): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x0C);

        return $this;
    }

    public function writeChar(string $char): self
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
        // usleep(1);
        $this->enable->turnOn();
        // usleep(1);
        $this->enable->turnOff();
        // usleep(100); // commands need >37us to settle

        return $this;
    }

    protected function writeData(int $data): self
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
        $this->data4->setLevel(($data & 0b0001) ? Level::HIGH : Level::LOW);
        $this->data5->setLevel(($data & 0b0010) ? Level::HIGH : Level::LOW);
        $this->data6->setLevel(($data & 0b0100) ? Level::HIGH : Level::LOW);
        $this->data7->setLevel(($data & 0b1000) ? Level::HIGH : Level::LOW);

        $this->pulseEnable();
    }

    public function clearDisplay(): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x01);

        usleep(100);

        return $this;
    }

    /**
     * Move the cursor to the beginning of the first line
     */
    public function home(): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x02);

        usleep(100);

        return $this;
    }

    /**
     * Move the cursor one position to the left
     */
    public function shiftLeft(): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x18);

        usleep(100);

        return $this;
    }

    /**
     * Move the cursor one position to the left
     */
    public function shiftRight(): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x1C);

        usleep(100);

        return $this;
    }

    /**
     * Move the cursor to the specified position
     */
    public function setCursor(int $row, int $column): self
    {
        $this->instructionInputMode();
        $this->write8Bits(0x80 | ($row * 0x40 + $column));

        usleep(100);

        return $this;
    }

    /**
     * Write a string to the display
     */
    public function writeString(string $string): self
    {
        foreach (str_split($string) as $char) {
            $this->writeChar($char);
        }

        return $this;
    }
}
