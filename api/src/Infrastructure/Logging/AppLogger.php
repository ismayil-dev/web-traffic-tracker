<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class AppLogger
{
    private static ?Logger $logger = null;

    public static function instance(): Logger
    {
        if (!self::$logger) {
            $logPath = __DIR__.'/../../../logs/app.log';
            $stream = new StreamHandler($logPath, Level::Debug);

            $dateFormat = 'Y-m-d H:i:s';
            $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

            $formatter = new LineFormatter($output, $dateFormat, true, true);
            $stream->setFormatter($formatter);

            self::$logger = new Logger('app');
            self::$logger->pushHandler($stream);
        }

        return self::$logger;
    }
}
