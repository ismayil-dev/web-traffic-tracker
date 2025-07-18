<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$envFile = __DIR__ . '/../.env.testing';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
    $dotenv->load();
}