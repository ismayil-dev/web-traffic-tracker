<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Exception;

use TrafficTracker\Shared\Exception\UserFriendlyException;
use Exception;

class InvalidApiKey extends Exception implements UserFriendlyException
{
    public function __construct()
    {
        parent::__construct('Invalid API key');
    }
}
