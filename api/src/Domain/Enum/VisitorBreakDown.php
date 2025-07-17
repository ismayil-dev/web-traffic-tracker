<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Enum;

enum VisitorBreakDown: string
{
    case BROWSER = 'browser';
    case OS = 'os';
    case DEVICE = 'device';

    public function getPayloadKey(): string
    {
        return match ($this) {
            self::BROWSER => 'browser',
            self::OS => 'os',
            self::DEVICE => 'device',
        };
    }
}
