<?php

namespace DanJohnson95\Pinout;

use DanJohnson95\Pinout\Shell\Commandable;
use DanJohnson95\Pinout\Shell\SysFile;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/pinout.php',
            'pinout',
        );

        $this->app->bind(Commandable::class, config('pinout.sys_file'));
    }

    public function boot()
    {
        $this->commands([
            Console\GetCommand::class,
            Console\OnCommand::class,
            Console\OffCommand::class,
            Console\ListenInterruptsCommand::class,
            Console\StartCommand::class,
        ]);
    }
}
