<?php

namespace DanJohnson95\Pinout\Console;

use DanJohnson95\Pinout\Facades\PinService;
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

        $pin = PinService::pin($this->argument('pin'));

        if (! $pin->isOutput()) {
            $this->error("Pin {$pin->pinNumber} isn't configured for output");
            return 1;
        }

        $pin->turnOn();

        $this->info("Pin {$pin->pinNumber} is currently {$pin->level->name}");
    }
}
