<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Exception;

use Exception;
use TrafficTracker\Shared\Exception\UserFriendlyException;

class DomainException extends Exception implements UserFriendlyException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }
}
