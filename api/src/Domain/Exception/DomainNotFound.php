<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Exception;

use Exception;
use TrafficTracker\Shared\Exception\UserFriendlyException;

class DomainNotFound extends Exception implements UserFriendlyException
{
    public function __construct()
    {
        parent::__construct('Domain not found', 404);
    }
}
