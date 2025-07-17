<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Repositories;

use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\ValueObject\ApiKey;

interface DomainRepositoryInterface
{
    public function save(Domain $domain): Domain;

    public function findById(int $id): ?Domain;

    public function findByDomain(string $domain): ?Domain;

    public function findByApiKey(ApiKey $apiKey): ?Domain;

    public function findAll(): array;

    public function exists(string $domain): bool;
}
