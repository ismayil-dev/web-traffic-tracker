<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Middleware;

use TrafficTracker\Domain\Exception\InvalidApiKey;
use TrafficTracker\Infrastructure\Service\JwtService;
use TrafficTracker\Presentation\Exception\ApiKeyMissing;

trait JwtTokenParser
{
    /**
     * @return array{user_id: int, email: string, iat: int, exp: int}
     *
     * @throws InvalidApiKey
     * @throws ApiKeyMissing
     */
    public function getJwtPayload(): array
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (empty($authHeader)) {
            throw new ApiKeyMissing();
        }

        if (!str_starts_with($authHeader, 'Bearer ')) {
            throw new InvalidApiKey();
        }

        [, $token] = explode(' ', $authHeader);

        $payload = JwtService::validateToken($token);

        if (!$payload) {
            throw new InvalidApiKey();
        }

        return $payload;
    }
}
