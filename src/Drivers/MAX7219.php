<?php

namespace DanJohnson95\Pinout\Drivers;

use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Level;

class MAX7219
{
    protected Pin $din;
    protected Pin $clk;
    protected Pin $cs;

    const NUMBER_OF_DIGITS = 8;

    const NO_OP = 0x00;
    const DIGIT_0 = 0x01;
    const DIGIT_1 = 0x02;
    const DIGIT_2 = 0x03;
    const DIGIT_3 = 0x04;
    const DIGIT_4 = 0x05;
    const DIGIT_5 = 0x06;
    const DIGIT_6 = 0x07;
    const DIGIT_7 = 0x08;
    const DECODE_MODE = 0x09;
    const INTENSITY = 0x0A;
    const SCAN_LIMIT = 0x0B;
    const SHUTDOWN = 0x0C;
    const DISPLAY_TEST = 0x0F;

    public static function make(
        Pin $din,
        Pin $clk,
        Pin $cs
    ): self {
        $instance = new self();
        $instance->din = $din;
        $instance->clk = $clk;
        $instance->cs = $cs;

        $instance->initialise();

        return $instance;
    }

    protected function shiftOut(int $data): void
    {
        for ($i = (self::NUMBER_OF_DIGITS - 1); $i >= 0; $i--) {
            $this->clk->turnOff();
            $this->din->setLevel(($data >> $i) & 1 ? Level::HIGH : Level::LOW);
            $this->clk->turnOn();
        }
    }

    protected function sendCommand(int $register, int $data): void
    {
        $this->cs->turnOff();
        $this->shiftOut($register);
        $this->shiftOut($data);
        $this->cs->turnOn();
    }

    protected function initialise(): void
    {
        $this->sendCommand(self::SCAN_LIMIT, 0x07);
        $this->sendCommand(self::DECODE_MODE, 0xFF);
        $this->sendCommand(self::SHUTDOWN, 0x01);
        $this->sendCommand(self::DISPLAY_TEST, 0x00);
        $this->clearDisplay();
        $this->setIntensity(5);
    }

    public function clearDisplay(): self
    {
        for ($i = self::DIGIT_0; $i <= self::DIGIT_7; $i++) {
            $this->sendCommand($i, 0x00);
        }

        return $this;
    }

    public function setIntensity(int $level): self
    {
        $level = max(0, min(15, $level));
        $this->sendCommand(self::INTENSITY, $level);

        return $this;
    }

    public function displayDigit(int $digit, int $value, bool $decimalPoint = false): self
    {
        if ($digit < 0 || $digit > (self::NUMBER_OF_DIGITS - 1)) {
            throw new \InvalidArgumentException("Digit must be between 0 and 7");
        }
        $value &= 0x0F;
        if ($decimalPoint) {
            $value |= 0x80;
        }

        $this->sendCommand(self::DIGIT_0 + $digit, $value);

        return $this;
    }

    public function displayNumber(int $number): self
    {
        $number = str_pad($number, self::NUMBER_OF_DIGITS, " ", STR_PAD_LEFT);
        for ($i = 0; $i < self::NUMBER_OF_DIGITS; $i++) {
            $this->displayDigit(self::NUMBER_OF_DIGITS - 1 - $i, ord($number[$i]));
        }

        return $this;
    }

    public function shutdown(bool $enable = true): void
    {
        $this->sendCommand(self::SHUTDOWN, $enable ? 0x00 : 0x01);
    }
}
