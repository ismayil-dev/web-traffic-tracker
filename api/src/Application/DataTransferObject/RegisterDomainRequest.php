<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Domain\Entity\User;

readonly class RegisterDomainRequest
{
    public function __construct(
        public User $user,
        public string $domain,
    ) {
    }
}
