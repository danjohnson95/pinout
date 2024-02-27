<?php

use DanJohnson95\Pinout\Drivers\SevenSegmentDisplay;
use DanJohnson95\Pinout\Pinout;

beforeEach(function () {
    Pinout::fake();

    $this->display = SevenSegmentDisplay::make(
        pinA: Pinout::pin(1),
        pinB: Pinout::pin(2),
        pinC: Pinout::pin(3),
        pinD: Pinout::pin(4),
        pinE: Pinout::pin(5),
        pinF: Pinout::pin(6),
        pinG: Pinout::pin(7),
        pinDP: Pinout::pin(8),
    );
});

it('clears the display', function () {
    $this->display->clearDisplay();

    Pinout::assertPinTurnedOff(1);
    Pinout::assertPinTurnedOff(2);
    Pinout::assertPinTurnedOff(3);
    Pinout::assertPinTurnedOff(4);
    Pinout::assertPinTurnedOff(5);
    Pinout::assertPinTurnedOff(6);
    Pinout::assertPinTurnedOff(7);
});

it('shows the decimal point', function () {
    $this->display->showDecimalPoint();

    Pinout::assertPinTurnedOn(8);
});

it('hides the decimal point', function () {
    $this->display->hideDecimalPoint();

    Pinout::assertPinTurnedOff(8);
});

it('renders a 1', function () {
    $this->display->renderInteger(1);

    Pinout::assertPinTurnedOff(1);
    Pinout::assertPinTurnedOn(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOff(4);
    Pinout::assertPinTurnedOff(5);
    Pinout::assertPinTurnedOff(6);
    Pinout::assertPinTurnedOff(7);
});

it('renders a 2', function () {
    $this->display->renderInteger(2);

    Pinout::assertPinTurnedOn(1);
    Pinout::assertPinTurnedOn(2);
    Pinout::assertPinTurnedOff(3);
    Pinout::assertPinTurnedOn(4);
    Pinout::assertPinTurnedOn(5);
    Pinout::assertPinTurnedOff(6);
    Pinout::assertPinTurnedOn(7);
});

it('renders a 3', function () {
    $this->display->renderInteger(3);

    Pinout::assertPinTurnedOn(1);
    Pinout::assertPinTurnedOn(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOn(4);
    Pinout::assertPinTurnedOff(5);
    Pinout::assertPinTurnedOff(6);
    Pinout::assertPinTurnedOn(7);
});

it('renders a 4', function () {
    $this->display->renderInteger(4);

    Pinout::assertPinTurnedOff(1);
    Pinout::assertPinTurnedOn(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOff(4);
    Pinout::assertPinTurnedOff(5);
    Pinout::assertPinTurnedOn(6);
    Pinout::assertPinTurnedOn(7);
});

it('renders a 5', function () {
    $this->display->renderInteger(5);

    Pinout::assertPinTurnedOn(1);
    Pinout::assertPinTurnedOff(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOn(4);
    Pinout::assertPinTurnedOff(5);
    Pinout::assertPinTurnedOn(6);
    Pinout::assertPinTurnedOn(7);
});

it('renders a 6', function () {
    $this->display->renderInteger(6);

    Pinout::assertPinTurnedOn(1);
    Pinout::assertPinTurnedOff(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOn(4);
    Pinout::assertPinTurnedOn(5);
    Pinout::assertPinTurnedOn(6);
    Pinout::assertPinTurnedOn(7);
});

it('renders a 7', function () {
    $this->display->renderInteger(7);

    Pinout::assertPinTurnedOn(1);
    Pinout::assertPinTurnedOn(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOff(4);
    Pinout::assertPinTurnedOff(5);
    Pinout::assertPinTurnedOff(6);
    Pinout::assertPinTurnedOff(7);
});

it('renders a 8', function () {
    $this->display->renderInteger(8);

    Pinout::assertPinTurnedOn(1);
    Pinout::assertPinTurnedOn(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOn(4);
    Pinout::assertPinTurnedOn(5);
    Pinout::assertPinTurnedOn(6);
    Pinout::assertPinTurnedOn(7);
});

it('renders a 9', function () {
    $this->display->renderInteger(9);

    Pinout::assertPinTurnedOn(1);
    Pinout::assertPinTurnedOn(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOn(4);
    Pinout::assertPinTurnedOff(5);
    Pinout::assertPinTurnedOn(6);
    Pinout::assertPinTurnedOn(7);
});

it('renders a 0', function () {
    $this->display->renderInteger(0);

    Pinout::assertPinTurnedOn(1);
    Pinout::assertPinTurnedOn(2);
    Pinout::assertPinTurnedOn(3);
    Pinout::assertPinTurnedOn(4);
    Pinout::assertPinTurnedOn(5);
    Pinout::assertPinTurnedOn(6);
    Pinout::assertPinTurnedOff(7);
});
