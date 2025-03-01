<?php

use DanJohnson95\Pinout\Drivers\SevenSegmentDisplay;
use DanJohnson95\Pinout\Facades\PinService;

beforeEach(function () {
    PinService::fake();

    $this->display = SevenSegmentDisplay::make(
        pinA: PinService::pin(1),
        pinB: PinService::pin(2),
        pinC: PinService::pin(3),
        pinD: PinService::pin(4),
        pinE: PinService::pin(5),
        pinF: PinService::pin(6),
        pinG: PinService::pin(7),
        pinDP: PinService::pin(8),
    );
});

it('clears the display', function () {
    $this->display->clearDisplay();

    PinService::assertPinTurnedOff(1);
    PinService::assertPinTurnedOff(2);
    PinService::assertPinTurnedOff(3);
    PinService::assertPinTurnedOff(4);
    PinService::assertPinTurnedOff(5);
    PinService::assertPinTurnedOff(6);
    PinService::assertPinTurnedOff(7);
});

it('shows the decimal point', function () {
    $this->display->showDecimalPoint();

    PinService::assertPinTurnedOn(8);
});

it('hides the decimal point', function () {
    $this->display->hideDecimalPoint();

    PinService::assertPinTurnedOff(8);
});

it('renders a 1', function () {
    $this->display->renderInteger(1);

    PinService::assertPinTurnedOff(1);
    PinService::assertPinTurnedOn(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOff(4);
    PinService::assertPinTurnedOff(5);
    PinService::assertPinTurnedOff(6);
    PinService::assertPinTurnedOff(7);
});

it('renders a 2', function () {
    $this->display->renderInteger(2);

    PinService::assertPinTurnedOn(1);
    PinService::assertPinTurnedOn(2);
    PinService::assertPinTurnedOff(3);
    PinService::assertPinTurnedOn(4);
    PinService::assertPinTurnedOn(5);
    PinService::assertPinTurnedOff(6);
    PinService::assertPinTurnedOn(7);
});

it('renders a 3', function () {
    $this->display->renderInteger(3);

    PinService::assertPinTurnedOn(1);
    PinService::assertPinTurnedOn(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOn(4);
    PinService::assertPinTurnedOff(5);
    PinService::assertPinTurnedOff(6);
    PinService::assertPinTurnedOn(7);
});

it('renders a 4', function () {
    $this->display->renderInteger(4);

    PinService::assertPinTurnedOff(1);
    PinService::assertPinTurnedOn(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOff(4);
    PinService::assertPinTurnedOff(5);
    PinService::assertPinTurnedOn(6);
    PinService::assertPinTurnedOn(7);
});

it('renders a 5', function () {
    $this->display->renderInteger(5);

    PinService::assertPinTurnedOn(1);
    PinService::assertPinTurnedOff(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOn(4);
    PinService::assertPinTurnedOff(5);
    PinService::assertPinTurnedOn(6);
    PinService::assertPinTurnedOn(7);
});

it('renders a 6', function () {
    $this->display->renderInteger(6);

    PinService::assertPinTurnedOn(1);
    PinService::assertPinTurnedOff(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOn(4);
    PinService::assertPinTurnedOn(5);
    PinService::assertPinTurnedOn(6);
    PinService::assertPinTurnedOn(7);
});

it('renders a 7', function () {
    $this->display->renderInteger(7);

    PinService::assertPinTurnedOn(1);
    PinService::assertPinTurnedOn(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOff(4);
    PinService::assertPinTurnedOff(5);
    PinService::assertPinTurnedOff(6);
    PinService::assertPinTurnedOff(7);
});

it('renders a 8', function () {
    $this->display->renderInteger(8);

    PinService::assertPinTurnedOn(1);
    PinService::assertPinTurnedOn(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOn(4);
    PinService::assertPinTurnedOn(5);
    PinService::assertPinTurnedOn(6);
    PinService::assertPinTurnedOn(7);
});

it('renders a 9', function () {
    $this->display->renderInteger(9);

    PinService::assertPinTurnedOn(1);
    PinService::assertPinTurnedOn(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOn(4);
    PinService::assertPinTurnedOff(5);
    PinService::assertPinTurnedOn(6);
    PinService::assertPinTurnedOn(7);
});

it('renders a 0', function () {
    $this->display->renderInteger(0);

    PinService::assertPinTurnedOn(1);
    PinService::assertPinTurnedOn(2);
    PinService::assertPinTurnedOn(3);
    PinService::assertPinTurnedOn(4);
    PinService::assertPinTurnedOn(5);
    PinService::assertPinTurnedOn(6);
    PinService::assertPinTurnedOff(7);
});
