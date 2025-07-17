<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Domain\Entity\Domain;

readonly class RecentVisitsRequest
{
    public function __construct(
        public Domain $domain,
        public int $limit = 20,
    ) {

    }
}
