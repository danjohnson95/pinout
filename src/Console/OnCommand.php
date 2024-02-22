<?php

namespace DanJohnson95\Pinout\Console;

use DanJohnson95\Pinout\Pinout;
use Illuminate\Console\Command;

class OnCommand extends Command
{
    protected $signature = 'pinout:on {pin}';
    protected $description = 'Turns the given pin "on" (HIGH)';

    public function handle()
    {
        $pinNumber = $this->argument('pin');

        if (! is_numeric($pinNumber)) {
            $this->error('Pin must be a number');
            return 1;
        }

        $pin = Pinout::pin($this->argument('pin'))->turnOn();

        $this->info("Pin {$pin->pinNumber} is currently {$pin->level->name}");
    }
}
