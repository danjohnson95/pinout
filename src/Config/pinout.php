<?php

use DanJohnson95\Pinout\Shell\SysFile; 

return [

    /**
     * Here you must define which GPIO pins your application is going to use,
     * and the direction each one will have (input or output).
     */
    'pins' => [
        // For example,
        // 1 => Direction::OUTPUT,
        // 2 => Direction::INPUT,
    ],

    'php_bin' => '/usr/bin/php',

    'gpio_listener' => [
        'tmp_dir' => '/tmp/pinout/',
        'pid' => 1,
    ],

    'sys_file' => SysFile::class,
    'gpio_chip' => 'gpiochip0'
];
