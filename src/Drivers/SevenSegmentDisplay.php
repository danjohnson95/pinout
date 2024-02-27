<?php

namespace DanJohnson95\Pinout\Drivers;

use DanJohnson95\Pinout\Collections\PinCollection;
use DanJohnson95\Pinout\Entities\Pin;
use DanJohnson95\Pinout\Enums\Level;

class SevenSegmentDisplay
{
    protected Pin $pinA;
    protected Pin $pinB;
    protected Pin $pinC;
    protected Pin $pinD;
    protected Pin $pinE;
    protected Pin $pinF;
    protected Pin $pinG;
    protected ?Pin $pinDP;
    protected PinCollection $pins;

    protected const HEXMAP = [
        '0' => 0xC0,
        '1' => 0xF9,
        '2' => 0xA4,
        '3' => 0xB0,
        '4' => 0x99,
        '5' => 0x92,
        '6' => 0x82,
        '7' => 0xF8,
        '8' => 0x80,
        '9' => 0x90,
    ];

    public static function make(
        Pin $pinA,
        Pin $pinB,
        Pin $pinC,
        Pin $pinD,
        Pin $pinE,
        Pin $pinF,
        Pin $pinG,
        ?Pin $pinDP = null
    ): self {
        $display = new self();
        $display->pinA = $pinA;
        $display->pinB = $pinB;
        $display->pinC = $pinC;
        $display->pinD = $pinD;
        $display->pinE = $pinE;
        $display->pinF = $pinF;
        $display->pinG = $pinG;
        $display->pinDP = $pinDP;

        $display->pins = new PinCollection([$pinA, $pinB, $pinC, $pinD, $pinE, $pinF, $pinG]);

        if ($pinDP) {
            $display->pins->push($pinDP);
        }

        $display->pins->makeOutput();

        return $display;
    }

    public function renderInteger(int $integer): self
    {
        $this->renderHex(static::HEXMAP[$integer]);

        return $this;
    }

    public function showDecimalPoint(): self
    {
        $this->pinDP->setLevel(Level::HIGH);

        return $this;
    }

    public function hideDecimalPoint(): self
    {
        $this->pinDP->setLevel(Level::LOW);

        return $this;
    }

    public function clearDisplay(): self
    {
        $this->pins->turnOff();

        return $this;
    }

    protected function renderHex(int $hex): self
    {
        // Flip the hex values.
        $hex = ~$hex;

        $this->pinA->setLevel($hex & 0x01 ? Level::HIGH : Level::LOW);
        $this->pinB->setLevel($hex & 0x02 ? Level::HIGH : Level::LOW);
        $this->pinC->setLevel($hex & 0x04 ? Level::HIGH : Level::LOW);
        $this->pinD->setLevel($hex & 0x08 ? Level::HIGH : Level::LOW);
        $this->pinE->setLevel($hex & 0x10 ? Level::HIGH : Level::LOW);
        $this->pinF->setLevel($hex & 0x20 ? Level::HIGH : Level::LOW);
        $this->pinG->setLevel($hex & 0x40 ? Level::HIGH : Level::LOW);

        return $this;
    }
}
