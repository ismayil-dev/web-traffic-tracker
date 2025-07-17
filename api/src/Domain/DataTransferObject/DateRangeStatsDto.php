<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\DataTransferObject;

use TrafficTracker\Domain\Contract\AnalyticsStatsContract;
use TrafficTracker\Domain\ValueObject\DatePeriod;

readonly class DateRangeStatsDto implements AnalyticsStatsContract
{
    public function __construct(
        public DatePeriod $datePeriod,
        public int $uniqueVisitors,
        public int $uniquePages,
        public int $totalVisits,
        /** @var array<int, AnalyticsStatsDto> */
        public array $dailyBreakdown,
    ) {

    }

    public function toArray(): array
    {
        return [
            'from' => $this->datePeriod->from->format('Y-m-d'),
            'to' => $this->datePeriod->to->format('Y-m-d'),
            'unique_visitors' => $this->uniqueVisitors,
            'unique_pages' => $this->uniquePages,
            'total_visits' => $this->totalVisits,
            'daily_breakdown' => array_map(fn (AnalyticsStatsDto $stats) => $stats->toArray(), $this->dailyBreakdown),
        ];
    }
}
