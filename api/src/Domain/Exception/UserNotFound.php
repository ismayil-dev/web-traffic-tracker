<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Exception;

use Exception;
use TrafficTracker\Shared\Exception\UserFriendlyException;

class UserNotFound extends Exception implements UserFriendlyException
{
    public function __construct()
    {
        parent::__construct('User not found', 404);
    }
}
