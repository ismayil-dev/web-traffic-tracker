<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Repositories;

use TrafficTracker\Domain\Entity\Visit;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Domain\ValueObject\VisitorHash;

interface VisitRepositoryInterface
{
    public function save(Visit $visit): Visit;

    public function findById(int $id): ?Visit;

    public function findByDomain(int $domainId, ?DatePeriod $datePeriod = null): array;

    public function findByUrl(int $domainId, string $baseUrl, ?DatePeriod $datePeriod = null): array;

    public function findByVisitor(int $domainId, VisitorHash $visitorHash): array;

    public function countByDomain(int $domainId, ?DatePeriod $datePeriod = null): int;

    public function countUniqueVisitors(int $domainId, ?DatePeriod $datePeriod = null): int;

    public function countUniquePages(int $domainId, ?DatePeriod $datePeriod = null): int;

    public function getPopularPages(int $domainId, int $limit = 10, ?DatePeriod $datePeriod = null): array;

    public function getBrowserStats(int $domainId, ?DatePeriod $datePeriod = null): array;

    public function getOSStats(int $domainId, ?DatePeriod $datePeriod = null): array;

    public function getDeviceStats(int $domainId, ?DatePeriod $datePeriod = null): array;

    public function getRecentVisits(int $domainId, int $limit = 20): array;
}
