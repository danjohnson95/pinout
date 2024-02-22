<?php

namespace DanJohnson95\Pinout;

use DanJohnson95\Pinout\Console\BenchmarkCommand;
use DanJohnson95\Pinout\Shell\Commandable;
use DanJohnson95\Pinout\Shell\RaspiGpio;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->bind(Commandable::class, RaspiGpio::class);
    }

    public function boot()
    {
        $this->commands([
            Console\GetCommand::class,
            Console\OnCommand::class,
            Console\OffCommand::class,
        ]);
    }
}
