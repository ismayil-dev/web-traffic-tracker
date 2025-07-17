<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Exception;

use Exception;

class MethodNotAllowed extends Exception
{
    public function __construct(string $uri)
    {
        parent::__construct("Method Not Allowed: {$uri}", 405);
    }
}
