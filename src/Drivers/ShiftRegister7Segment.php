<?php

namespace DanJohnson95\Pinout\Drivers;

use DanJohnson95\Pinout\Entities\Pin;

class ShiftRegister7Segment extends SevenSegmentDisplay
{
    public function __construct(
        protected Pin $dataPin,
        protected Pin $clockPin,
        protected Pin $latchPin,
        protected int $numDigits = 1,

    ) {
        $this->dataPin->makeOutput();
        $this->clockPin->makeOutput();
        $this->latchPin->makeOutput();
    }

    public function renderHex(int $hex): self
    {
        $this->writeToShiftRegister($hex);

        return $this;
    }

    public function writeToShiftRegister(int $value): void
    {
        $this->latchLow();
        $this->shiftOut($value);
        $this->latchHigh();
    }

    protected function shiftOut(int $value): void
    {
        for ($i = 0; $i < (8 * $this->numDigits); $i++) {
            $this->clockLow();

            if ($value & (1 << $i)) {
                $this->dataPin->turnOn();
                dump('1');
            } else {
                $this->dataPin->turnOff();
                dump('0');
            }

            $this->clockHigh();
        }
    }

    protected function latchLow(): void
    {
        $this->latchPin->turnOff();
    }

    protected function clockLow(): void
    {
        $this->clockPin->turnOff();
    }

    protected function clockHigh(): void
    {
        $this->clockPin->turnOn();
    }

    protected function latchHigh(): void
    {
        $this->latchPin->turnOn();
    }

}
