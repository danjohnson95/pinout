<?php

namespace DanJohnson95\Pinout\Console;

use DanJohnson95\Pinout\Enums\Func;
use DanJohnson95\Pinout\Enums\Level;
use DanJohnson95\Pinout\Shell\Commandable;
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

        // Check the options are valid first...

        if ($func = $this->option('func')) {
            $func = Func::tryFrom($func);
        }

        if (($level = $this->option('level')) !== null) {
            $level = Level::tryFrom($level);
        }

        // Now set them.
        if ($func) {
            app(Commandable::class)->setFunction($pinNumber, $func);
        }

        if ($level) {
            app(Commandable::class)->setLevel($pinNumber, $level);
        }

        $state = PinService::pin($this->argument('pin'));

        $levelText = $level?->value ?? $level;
        $this->info("Pin {$state->pinNumber} is currently {$levelText}");
    }
}
