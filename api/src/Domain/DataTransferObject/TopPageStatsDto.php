<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\DataTransferObject;

readonly class TopPageStatsDto
{
    public function __construct(
        public string $url,
        public string $title,
        public int $visits,
        public int $uniqueVisitors,
    ) {
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'visits' => $this->visits,
            'unique_visitors' => $this->uniqueVisitors,
        ];
    }
}
