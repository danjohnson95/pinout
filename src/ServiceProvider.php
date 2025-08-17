<?php

namespace DanJohnson95\Pinout;

use DanJohnson95\Pinout\Shell\Commandable;
use DanJohnson95\Pinout\Shell\SysFile;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->bind(Commandable::class, SysFile::class);

        $this->mergeConfigFrom(
            __DIR__ . '/Config/pinout.php',
            'pinout',
        );
    }

    public function boot()
    {
        $this->commands([
            Console\GetCommand::class,
            Console\OnCommand::class,
            Console\OffCommand::class,
            Console\SetCommand::class,
            Console\ListenInterruptsCommand::class,
            Console\StartCommand::class,
        ]);
    }
}
