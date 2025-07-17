<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Exception;

use Exception;
use TrafficTracker\Shared\Exception\UserFriendlyException;

class VisitRejected extends Exception implements UserFriendlyException
{
    public function __construct()
    {
        parent::__construct('Visit rejected (bot traffic or invalid request)', 403);
    }
}
