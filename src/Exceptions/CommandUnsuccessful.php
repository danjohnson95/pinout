<?php

namespace DanJohnson95\Pinout\Exceptions;

use Exception;

class CommandUnsuccessful extends Exception
{
    public function __construct(string $command, int $exitCode, string $output)
    {
        parent::__construct("Command '{$command}' was unsuccessful with exit code {$exitCode} and output: {$output}");
    }
}
