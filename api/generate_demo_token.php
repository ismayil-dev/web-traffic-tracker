<?php

require_once 'vendor/autoload.php';

use TrafficTracker\Infrastructure\Service\JwtService;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$demoUserId = 1;
$demoUserEmail = 'admin@yomali.com';

$token = JwtService::generateToken($demoUserId, $demoUserEmail);

echo $token . "\n\n";