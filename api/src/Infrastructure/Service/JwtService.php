<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtService
{
    private const string ALGORITHM = 'HS256';
    private const int EXPIRATION_TIME = 3600 * 24;

    public static function generateToken(int $userId, string $email): string
    {
        $payload = [
            'user_id' => $userId,
            'email' => $email,
            'iat' => time(),
            'exp' => time() + self::EXPIRATION_TIME,
        ];

        return JWT::encode($payload, static::getSecretKey(), self::ALGORITHM);
    }

    public static function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key(static::getSecretKey(), self::ALGORITHM));

            return (array) $decoded;
        } catch (Exception) {
            return null;
        }
    }

    private static function getSecretKey(): string
    {
        return $_ENV['JWT_SECRET'];
    }
}
