<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Enum;

enum Browser: string
{
    case MSIE = 'internet_explorer';
    case EDGE = 'microsoft_edge';
    case CHROME = 'chrome';
    case FIREFOX = 'firefox';
    case SAFARI = 'safari';
    case OPERA = 'opera';
    case UNKNOWN = 'unknown';

    public static function getRegexPattern(): array
    {
        return [
            'Edg/' => self::EDGE,
            'Chrome/' => self::CHROME,
            'Firefox/' => self::FIREFOX,
            'Safari/' => self::SAFARI,
            'Opera/' => self::OPERA,
            'OPR/' => self::OPERA,
            'Trident/' => self::MSIE,
            'MSIE' => self::MSIE,
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::MSIE => 'Internet Explorer',
            self::EDGE => 'Microsoft Edge',
            self::CHROME => 'Chrome',
            self::FIREFOX => 'Firefox',
            self::SAFARI => 'Safari',
            self::OPERA => 'Opera',
            default => 'Unknown',
        };
    }
}
