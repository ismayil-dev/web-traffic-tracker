<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Middleware;

use TrafficTracker\Domain\Exception\InvalidApiKey;
use TrafficTracker\Domain\Exception\UserNotFound;
use TrafficTracker\Domain\Service\UserService;
use TrafficTracker\Infrastructure\Repository\UserRepository;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Presentation\Exception\ApiKeyMissing;

class UserToken
{
    use JwtTokenParser;

    /**
     * @throws InvalidApiKey
     * @throws ApiKeyMissing
     * @throws UserNotFound
     */
    public function handle(): void
    {
        $payload = $this->getJwtPayload();

        $service = new UserService(new UserRepository());
        $user = $service->retrieveUser($payload['user_id']);

        RequestContext::setUser($user);
    }
}
