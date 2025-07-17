<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Entity;

use TrafficTracker\Domain\ValueObject\ApiKey;
use DateTimeImmutable;
use stdClass;
use DateMalformedStringException;

class Domain
{
    private DateTimeImmutable $createdAt;

    public function __construct(
        private readonly int $id,
        private readonly int $userId,
        private readonly string $domain,
        private ApiKey $apiKey,
        ?DateTimeImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public static function create(int $userId, string $domain, ApiKey $apiKey): self
    {
        return new self(0, $userId, $domain, $apiKey);
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromPdoStd(stdClass $std): self
    {
        return new self(
            id: $std->id,
            userId: $std->user_id,
            domain: $std->domain,
            apiKey: ApiKey::fromHash($std->api_key),
            createdAt: !is_null($std->created_at) ? new DateTimeImmutable($std->created_at) : null,
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getApiKey(): ApiKey
    {
        return $this->apiKey;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function regenerateApiKey(): void
    {
        $this->apiKey = ApiKey::generate();
    }
}
