<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Contract;

interface AnalyticsStatsContract
{
    public function toArray(): array;
}
