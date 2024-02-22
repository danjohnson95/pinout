<p align="center"><img width="25%" src="/art/laravel-pinout.png?1" alt="Pinout logo"></p>

# Laravel Pinout

Connect your Laravel application to the physical world with Laravel Pinout, where code meets circuitry. **Hardware and web, seamlessly united** 🤝

## Features

With a Laravel application running on supported hardware, you can:

- Get the current state of any GPIO pin
- Set the state of any GPIO pin

These basic features open up a world of possibilities, from simple LED control to complex robotics.

## Hardware support

At the moment, Laravel Pinout supports the following hardware:

| Model | Supported |
| --- | --- |
| Raspberry Pi Model A | ✅ |
| Raspberry Pi Model B | ✅ |
| Raspberry Pi Model B+ | ✅ |
| Raspberry Pi Model 2 | ✅ |
| Raspberry Pi 3 Model B | ✅ |
| Raspberry Pi 3 Model B+ | ✅ |
| Raspberry Pi 3 Model A+ | ✅ |
| Raspberry Pi 4 Model B | ✅ |
| Raspberry Pi 400 | ✅ |
| Raspberry Pi 5 | ✅ |
| Raspberry Pi Zero | ✅ |
| Raspberry Pi Zero v1.3 | ✅ |
| Raspberry Pi Zero W | ✅ |
| Raspberry Pi Zero WH | ✅ |
| Raspberry Pi Zero 2 W | ✅ |

## Getting started

Install the package to an existing Laravel project:

```
composer require danjohnson95/laravel-pinout
```

If you're using Laravel 11 or later, the package will be auto-discovered. If you're using an earlier version, you'll need to add the service provider to your `config/app.php` file:

```php
'providers' => [
    // ...
    DanJohnson95\Pinout\ServiceProvider::class,
    // ...
],
```

## Usage

This package allows you to interact with hardware using the `Pinout` facade, and also comes with a couple of Artisan commands for convenience.

```php
use DanJohnson95\Pinout\Facades\Pin;

$pin = Pinout::pin(17);

$pin->isOn(); // Whether the pin is "on" (high)
$pin->isOff(); // Whether the pin is "off" (low)
$pin->turnOn(); // Set the pin to "on"
$pin->turnOff(); // Set the pin to "off"
$pin->makeInput(); // Set the pin to input mode
$pin->makeOutput(); // Set the pin to output mode
```

## Roadmap

- [ ] Hardware interrupts
- [ ] I2C
- [ ] SPI
