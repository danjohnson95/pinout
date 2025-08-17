<?php

namespace DanJohnson95\Pinout\Console;

use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Facades\PinService;
use Illuminate\Console\Command;

class SetCommand extends Command
{
    protected $signature = 'pinout:set {pin} {--func=} {--level=}';
    protected $description = 'Sets the state of the pin';

    public function handle()
    {
        $pinNumber = $this->argument('pin');

        if (! is_numeric($pinNumber)) {
            $this->error('Pin must be a number');
            return 1;
        }

        $pin = PinService::pin($this->argument('pin'));

        if (($func = $this->option('func')) !== null) {
            $func = Func::tryFrom($func);
        }

        if (($level = $this->option('level')) !== null) {
            $level = Level::tryFrom($level);
        }

        // Now set them.
        if ($func) {
            PinService::setFunction($pin, $func);
        }

        if ($level) {
            PinService::setLevel($pin, $level);
        }

        $this->info("Pin {$pin->pinNumber} is currently {$level->value}");
    }
}
