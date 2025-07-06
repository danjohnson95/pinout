<?php

namespace DanJohnson95\Pinout;

use DanJohnson95\Pinout\Shell\Commandable;
use DanJohnson95\Pinout\Devices\SPIBus;
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
        $this->app->bind(SPIBus::class, function ($app, $parameters) {
            return SPIBus::make(
                chipSelect: $parameters['chipSelect'],
                clock: $parameters['clock'],
                dataIn: $parameters['dataIn'],
                dataOut: $parameters['dataOut'],
                mode: $parameters['mode']
            );
        });
    }

    public function boot()
    {
        $this->commands([
            Console\GetCommand::class,
            Console\OnCommand::class,
            Console\OffCommand::class,
            Console\ListenInterruptsCommand::class,
            Console\StartCommand::class,
            Console\SetCommand::class,
        ]);
    }
}
