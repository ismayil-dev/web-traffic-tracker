<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Exception;

use TrafficTracker\Shared\Exception\UserFriendlyException;
use Exception;

class DatePeriodIsRequired extends Exception implements UserFriendlyException
{
    public function __construct()
    {
        parent::__construct('Date period is required');
    }
}
