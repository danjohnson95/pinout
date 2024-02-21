<?php

namespace DanJohnson95\Pinout\Exceptions;

use Exception;

class CommandUnavailable extends Exception
{
    public function __construct()
    {
        parent::__construct('The `raspi-gpio` command is not available. Please install it using `sudo apt install raspi-gpio`.');
    }
}
