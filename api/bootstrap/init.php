<?php

declare(strict_types=1);

use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Logging\AppLogger;
use TrafficTracker\Shared\Exception\UserFriendlyException;

ini_set('display_errors', '1');

require_once __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

if (in_array($_ENV['APP_ENV'], ['local', 'dev'])) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}


set_exception_handler(function (Throwable $e) {
    AppLogger::instance()->error(get_class($e).': '.$e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);


    if (in_array($_ENV['APP_ENV'], ['local', 'dev'])) {
        $code = $e->getCode() ?: 500;
        $response = Response::error($e->getMessage(), $code);
        $response->toJson(append: [
            'exception' => [
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
        ]);
    } else {
        switch (true) {
            case $e instanceof UserFriendlyException:
                $code = $e->getCode() ?: 500;
                Response::error($e->getMessage(), $code)->toJson();
                break;
            default:
                Response::error('Something went wrong. We apologize for the inconvenience. Please try again later.', 500)->toJson();
                break;
        }
    }
});
