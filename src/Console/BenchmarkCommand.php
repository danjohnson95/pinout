<?php

namespace DanJohnson95\Pinout\Console;

use DanJohnson95\Pinout\Pinout;
use Illuminate\Console\Command;

class BenchmarkCommand extends Command
{
    protected $signature = 'pinout:benchmark {pin}';
    protected $description = 'Performs a benchmark';

    public function handle()
    {
        $pinNumber = $this->argument('pin');

        if (! is_numeric($pinNumber)) {
            $this->error('Pin must be a number');
            return 1;
        }


        $now = microtime();
        $pin = Pinout::pin($this->argument('pin'));

        $this->progressStart(10000);

        for ($i = 0; $i < 10000; $i++) {
            $pin->turnOn();
            $pin->turnOff();
            $this->progressAdvance();
        }

        $this->progressFinish();

        $this->info("Benchmark took " . (microtime() - $now) . " seconds");
    }
}
