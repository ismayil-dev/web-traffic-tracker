<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Domain\ValueObject\DatePeriod;

readonly class GetAnalyticsRequest
{
    public function __construct(
        public Period $period,
        public ?DatePeriod $datePeriod = null,
    ) {
    }
}
