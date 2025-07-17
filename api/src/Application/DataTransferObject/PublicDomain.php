<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Domain\Entity\Domain;

readonly class PublicDomain
{
    public function __construct(
        private Domain $domain,
        private string $apiKey,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->domain->getId(),
            'user_id' => $this->domain->getUserId(),
            'domain' => $this->domain->getDomain(),
            'api_key' => $this->apiKey,
        ];
    }
}
