<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Middleware;

use TrafficTracker\Domain\Exception\DomainNotFound;
use TrafficTracker\Domain\Exception\InvalidApiKey;
use TrafficTracker\Domain\Exception\UserNotFound;
use TrafficTracker\Domain\Service\DomainService;
use TrafficTracker\Domain\Service\UserService;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Repository\DomainRepository;
use TrafficTracker\Infrastructure\Repository\UserRepository;
use TrafficTracker\Presentation\Exception\ApiKeyMissing;

class UserTokenWithDomain
{
    use JwtTokenParser;

    /**
     * @throws InvalidApiKey
     * @throws ApiKeyMissing
     * @throws DomainNotFound
     * @throws UserNotFound
     */
    public function handle(): void
    {
        $payload = $this->getJwtPayload();

        $domainId = $_SERVER['HTTP_X_DOMAIN_ID'] ?? '';

        if (empty($domainId)) {
            throw new DomainNotFound();
        }

        $domainService = new DomainService(new DomainRepository());
        $domain = $domainService->findById((int) $domainId);

        if ($domain->getUserId() !== $payload['user_id']) {
            throw new InvalidApiKey();
        }

        $service = new UserService(new UserRepository());
        $user = $service->retrieveUser($payload['user_id']);

        RequestContext::setUser($user);
        RequestContext::setDomain($domain);
    }
}
