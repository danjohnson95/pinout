<?php

namespace DanJohnson95\Pinout\Enums;

enum Func: string
{
    case INPUT = 'INPUT';
    case OUTPUT = 'OUTPUT';
    case SDA1 = 'SDA1';
    case SCL1 = 'SCL1';
    case CTS0 = 'CTS0';
    case RTS0 = 'RTS0';
    case TXD0 = 'TXD0';
    case RXD0 = 'RXD0';
    case SD1_CLK = 'SD1_CLK';
    case SD1_CMD = 'SD1_CMD';
    case SD1_DAT0 = 'SD1_DAT0';
    case SD1_DAT1 = 'SD1_DAT1';
    case SD1_DAT2 = 'SD1_DAT2';
    case SD1_DAT3 = 'SD1_DAT3';
    case PWM0 = 'PWM0';
    case PWM1 = 'PWM1';
    case GPCLK1 = 'GPCLK1';
    case GPCLK2 = 'GPCLK2';
}
