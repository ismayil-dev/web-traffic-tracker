<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Middleware;

class Cors
{
    public function handle(): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? null;

        if ($origin) {
            header("Access-Control-Allow-Origin: $origin");
        } else {
            header('Access-Control-Allow-Origin: *'); // development only
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Domain, X-Domain-Id, Accept, Origin, User-Agent');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
