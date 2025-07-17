<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Exception;

use RuntimeException;

class DomainIsNotSet extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Domain is not set');
    }
}
