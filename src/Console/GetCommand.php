<?php

namespace DanJohnson95\Pinout\Console;

use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Facade as Pin;
use Illuminate\Console\Command;

class GetCommand extends Command
{
    protected $signature = 'pinout:get {pin}';
    protected $description = 'Gets the state of the pin';

    public function handle()
    {
        $pinNumber = $this->argument('pin');

        if (! is_numeric($pinNumber)) {
            $this->error('Pin must be a number');
            return 1;
        }

        $state = Pin::get($this->argument('pin'));

        $level = match($state->level) {
            Level::LOW => 'LOW',
            Level::HIGH => 'HIGH',
            default => 'UNKNOWN',
        };

        $this->info("Pin {$state->pin} is currently {$level}");
    }
}
