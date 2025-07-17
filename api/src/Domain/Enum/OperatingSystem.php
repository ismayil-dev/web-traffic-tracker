<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Enum;

enum OperatingSystem: string
{
    case WINDOWS = 'windows';
    case MACOS = 'macos';
    case LINUX = 'linux';
    case ANDROID = 'android';
    case IOS = 'ios';
    case UNKNOWN = 'unknown';

    public static function getRegexPattern(): array
    {
        return [
            '/Windows NT (\d+\.\d+)/' => self::WINDOWS,
            '/Mac OS X (\d+[._]\d+([._]\d+)?)/' => self::MACOS,
            '/Linux/' => self::LINUX,
            '/Android (\d+\.\d+)/' => self::ANDROID,
            '/iPhone OS (\d+[._]\d+([._]\d+)?)/' => self::IOS,
            '/iPad.*OS (\d+[._]\d+([._]\d+)?)/' => self::IOS,
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::WINDOWS => 'Windows',
            self::MACOS => 'macOS',
            self::LINUX => 'Linux',
            self::ANDROID => 'Android',
            self::IOS => 'iOS',
            default => 'Unknown',
        };
    }
}
