<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Middleware;

use TrafficTracker\Application\Handler\DomainValidator;
use TrafficTracker\Domain\Exception\DomainNotFound;
use TrafficTracker\Domain\Exception\InvalidApiKey;
use TrafficTracker\Domain\Service\DomainService;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Repository\DomainRepository;
use TrafficTracker\Presentation\Exception\ApiKeyMissing;

class DomainApiKey
{
    /**
     * @throws ApiKeyMissing
     * @throws DomainNotFound
     * @throws InvalidApiKey
     */
    public function handle(): void
    {
        $authKey = $_SERVER['HTTP_AUTHORIZATION'];

        if (empty($authKey)) {
            throw new ApiKeyMissing();
        }

        [, $apiKey] = explode(' ', $authKey);
        $domainService = new DomainService(new DomainRepository());
        $domainValidator = new DomainValidator($domainService);
        $domain = $domainValidator->execute($apiKey);

        RequestContext::setDomain($domain);
    }
}
