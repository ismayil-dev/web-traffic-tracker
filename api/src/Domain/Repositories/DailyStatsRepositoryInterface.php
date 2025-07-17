<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Repositories;

use TrafficTracker\Domain\Entity\DailyStats;
use DateTimeImmutable;
use TrafficTracker\Domain\ValueObject\DatePeriod;

interface DailyStatsRepositoryInterface
{
    public function save(DailyStats $stats): DailyStats;

    public function update(DailyStats $stats): DailyStats;

    public function findById(int $id): ?DailyStats;

    public function findByDate(int $domainId, DateTimeImmutable $date): ?DailyStats;

    public function findByDateRange(int $domainId, DatePeriod $datePeriod): array;
}
