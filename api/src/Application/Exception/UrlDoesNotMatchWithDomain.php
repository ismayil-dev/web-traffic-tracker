<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Exception;

use Exception;
use TrafficTracker\Shared\Exception\UserFriendlyException;

class UrlDoesNotMatchWithDomain extends Exception implements UserFriendlyException
{
    public function __construct()
    {
        parent::__construct('URL does not match authenticated domain', 403);
    }
}
