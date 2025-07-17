<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Exception\DomainNotFound;
use TrafficTracker\Domain\Exception\InvalidApiKey;
use TrafficTracker\Domain\Service\DomainService;

readonly class DomainValidator
{
    public function __construct(
        private DomainService $domainService,
    ) {
    }

    /**
     * @throws DomainNotFound
     * @throws InvalidApiKey
     */
    public function execute(string $apiKey): Domain
    {
        return $this->domainService->authenticateDomain($apiKey);
    }
}
