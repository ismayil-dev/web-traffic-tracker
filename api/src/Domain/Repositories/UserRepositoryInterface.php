<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Repositories;

use TrafficTracker\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
}
