<?php

namespace DanJohnson95\Pinout\Console;

use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Pinout;
use Illuminate\Console\Command;

class SetCommand extends Command
{
    protected $signature = 'pinout:set {pin} {--func} {--level}';
    protected $description = 'Sets the state of the pin';

    public function handle()
    {
        $pinNumber = $this->argument('pin');

        if (! is_numeric($pinNumber)) {
            $this->error('Pin must be a number');
            return 1;
        }

        // Check the options are valid first...

        if ($func = $this->option('func')) {
            $func = Func::tryFrom($func);
        }

        if ($level = $this->option('level')) {
            $level = Level::tryFrom($level);
        }

        // Now set them.
        if ($func) {
            Pinout::setFunction($pinNumber, $func);
        }

        if ($level) {
            Pinout::setLevel($pinNumber, $level);
        }

        $state = Pinout::get($this->argument('pin'));

        $this->info("Pin {$state->pinNumber} is currently {$level}");
    }
}
