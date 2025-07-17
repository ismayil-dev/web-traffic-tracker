<?php

declare(strict_types=1);


use TrafficTracker\Infrastructure\Http\Router;
use TrafficTracker\Infrastructure\Logging\AppLogger;

require __DIR__.'/../bootstrap/init.php';

AppLogger::instance()->info('Starting application...');

$router = new Router();
$router->load($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
