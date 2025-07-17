<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Enum;

enum Device: string
{
    case IPHONE = 'iphone';
    case IPAD = 'ipad';
    case IPOD = 'ipod';
    case ANDROID_PHONE = 'android_phone';
    case ANDROID_TABLET = 'android_tablet';
    case BLACKBERRY = 'blackberry';
    case WINDOWS_PHONE = 'windows_phone';
    case DESKTOP = 'desktop';

    public static function getRegexPattern(): array
    {
        return [
            '/iPhone/' => self::IPHONE,
            '/iPad/' => self::IPAD,
            '/iPod/' => self::IPOD,
            '/Android.*Mobile/' => self::ANDROID_PHONE,
            '/Android/' => self::ANDROID_TABLET,
            '/BlackBerry/' => self::BLACKBERRY,
            '/Windows Phone/' => self::WINDOWS_PHONE,
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::IPHONE => 'iPhone',
            self::IPAD => 'iPad',
            self::IPOD => 'iPod',
            self::ANDROID_PHONE => 'Android Phone',
            self::ANDROID_TABLET => 'Android Tablet',
            self::BLACKBERRY => 'BlackBerry',
            self::WINDOWS_PHONE => 'Windows Phone',
            default => 'Desktop',
        };
    }
}
