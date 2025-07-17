<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Domain\DataTransferObject\TopPageStatsDto;

readonly class AnalyticsTopPages
{
    /**
     * @param array<TopPageStatsDto> $topPages
     */
    public function __construct(
        public array $topPages,
    ) {
    }

    public function toArray(): array
    {
        return array_map(function ($page) {
            return $page->toArray();
        }, $this->topPages);
    }
}
