<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Repositories;

use TrafficTracker\Domain\Entity\Visitor;
use TrafficTracker\Domain\ValueObject\VisitorHash;

interface VisitorRepositoryInterface
{
    public function save(Visitor $visitor): Visitor;

    public function update(Visitor $visitor): Visitor;

    public function findById(int $id): ?Visitor;

    public function findByHash(int $domainId, VisitorHash $visitorHash): ?Visitor;

    public function exists(int $domainId, VisitorHash $visitorHash): bool;

    public function getBrowserStats(int $domainId): array;

    public function getOSStats(int $domainId): array;

    public function getDeviceStats(int $domainId): array;
}
