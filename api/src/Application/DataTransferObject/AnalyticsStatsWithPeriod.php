<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Domain\Contract\AnalyticsStatsContract;

readonly class AnalyticsStatsWithPeriod
{
    public function __construct(
        private Period $period,
        private AnalyticsStatsContract $stats,
    ) {

    }

    public function toArray(): array
    {
        return [
            'period' => $this->period->value,
            'stats' => $this->stats->toArray(),
        ];
    }
}
