<p align="center"><img width="25%" src="/art/laravel-pinout.png?1" alt="Pinout logo"></p>

# Pinout

Connect your Laravel application to the physical world with Pinout, where code meets circuitry. **Hardware and web, seamlessly united** 🤝

## Features

With a Laravel application running on supported hardware, you can:

- Get the current state of any GPIO pin
- Set the state of any GPIO pin

These basic features open up a world of possibilities, from simple LED control to complex robotics.

And with the included drivers, you can also:

- Display digits on a 7 segment display
- Display anything on a 16x2 LCD display

## Hardware support

At the moment, Pinout supports the following hardware:

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
composer require danjohnson95/pinout
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

### `Pinout` facade

This package allows you to interact with hardware using the `Pinout` facade, and also comes with a couple of Artisan commands for convenience.

Use the `pin` method to get a `Pin` instance for a specific pin:

```php
$pin = \DanJohnson95\Pinout\Pinout::pin(13);
```

The argument is a reference to the GPIO pin number. (The BCM pin number is used, not the physical pin number.) See [pinout.xyz](https://pinout.xyz) for a visual reference.

The `Pin` instance has methods for interacting with the pin:

```php
$pin->isOn(); // Whether the pin is "on" (high)
$pin->isOff(); // Whether the pin is "off" (low)
$pin->turnOn(); // Set the pin to "on"
$pin->turnOff(); // Set the pin to "off"
$pin->makeInput(); // Set the pin to input mode
$pin->makeOutput(); // Set the pin to output mode
```

The facade also has a `pins` method for pulling multiple pins at once:

```php
$pins = \DanJohnson95\Pinout\Pinout::pins(13, 19, 26);
```

This will return a `PinCollection` instance, which is a collection of `Pin` instances.

The `PinCollection` comes with some handy methods too:

```php
$pins->turnOn(); // Turns all pins on in the collection
$pins->turnOff(); // Turns all pins off in the collection
$pins->makeInput(); // Sets all pins to input mode
$pins->makeOutput(); // Sets all pins to output mode
$pins->findByPinNumber(13); // Returns the Pin instance for the given pin number
$pins->whereIsOn(); // Returns a collection of pins that are on
$pins->whereIsOff(); // Returns a collection of pins that are off
```

### Artisan commands

This package comes with a couple of Artisan commands for convenience:

```bash
php artisan pinout:pin 13
```

This will return the current state of the pin.

```bash
php artisan pinout:on 13
```

This will turn pin 13 on.

```bash
php artisan pinout:off 13
```

This will turn pin 13 off.

## Roadmap

- [ ] Hardware interrupts
- [ ] I2C
- [ ] SPI
