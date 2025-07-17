<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\DataTransferObject;

use DateTimeImmutable;
use TrafficTracker\Domain\Contract\AnalyticsStatsContract;

readonly class AnalyticsStatsDto implements AnalyticsStatsContract
{
    public function __construct(
        public DateTimeImmutable $date,
        public int $uniqueVisitors,
        public int $uniquePages,
        public int $totalVisits,
    ) {

    }

    public function toArray(): array
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'unique_visitors' => $this->uniqueVisitors,
            'unique_pages' => $this->uniquePages,
            'total_visits' => $this->totalVisits,
        ];
    }
}
