<?php declare(strict_types=1);

namespace TrafficTracker\Domain\DataTransferObject;

use DateTimeImmutable;

readonly class OverallAnalyticsStatsDto
{
    public function __construct(
        public int $total_visits,
        public int $unique_visitors,
        public DateTimeImmutable $trackingSince,
        public DateTimeImmutable $lastActivity,
    ) {
    }

    public function toArray(): array
    {
        return [
            'total_visits' => $this->total_visits,
            'unique_visitors' => $this->unique_visitors,
            'tracking_since' => $this->trackingSince->format('Y-m-d'),
            'last_activity' => $this->lastActivity->format('Y-m-d'),
        ];
    }
}