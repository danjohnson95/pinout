<?php

namespace DanJohnson95\Pinout\Enums;

enum SPIClockStage: int
{
    case READY = 0;
    case SAMPLED = 1;
}
