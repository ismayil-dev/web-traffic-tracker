<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Exception;

use RuntimeException;

class UserIsNotSet extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('User is not set');
    }
}
