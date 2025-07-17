<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Enum;

enum Period: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case CUSTOM = 'custom';

    public static function values(): array
    {
        return [
            self::DAILY->value,
            self::WEEKLY->value,
            self::MONTHLY->value,
            self::CUSTOM->value,
        ];
    }
}
