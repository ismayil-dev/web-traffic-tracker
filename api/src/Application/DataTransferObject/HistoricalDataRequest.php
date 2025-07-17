<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\ValueObject\DatePeriod;

readonly class HistoricalDataRequest
{
    public function __construct(
        public Domain $domain,
        public Period $period,
        public ?DatePeriod $datePeriod = null,
    ) {
    }
}
