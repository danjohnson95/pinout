<?php

namespace DanJohnson95\Pinout\Enums;

enum Level: int
{
    case LOW = 0;
    case HIGH = 1;

    public function toCache(): string 
    {
        return match($this) {
            Level::LOW => 'Level::LOW',
            Level::HIGH => 'Level::HIGH',
        };
    }

    public static function fromCache(
        string $cacheValue
    ): self 
    {
        return match($cacheValue) {
            'Level::LOW' => Level::LOW,
            'Level::HIGH' => Level::HIGH,
        };
    }
}
