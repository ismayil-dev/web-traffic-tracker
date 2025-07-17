<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Exception;

use Exception;

class DatabaseConnectionFailed extends Exception
{
    public function __construct()
    {
        parent::__construct('Database connection failed');
    }
}
