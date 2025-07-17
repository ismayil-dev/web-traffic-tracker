<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Exception;

use Exception;
use TrafficTracker\Shared\Exception\UserFriendlyException;

class ApiKeyMissing extends Exception implements UserFriendlyException
{
    public function __construct()
    {
        parent::__construct('API key is required');
    }
}
