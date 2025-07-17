<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Domain\Entity\DailyStats;

class HistoricalData
{
    public function __construct(
        public array $dailyStats,
    ) {
    }

    public function toArray(): array
    {
        return array_map(function (DailyStats $stats) {
            return [
                'date' => $stats->getDate()->format('Y-m-d'),
                'unique_visitors' => $stats->getUniqueVisitors(),
                'unique_pages' => $stats->getUniquePages(),
                'total_visits' => $stats->getTotalVisits(),
            ];
        }, $this->dailyStats);
    }
}
