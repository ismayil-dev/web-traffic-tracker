<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Entity;

use DateTimeImmutable;
use stdClass;
use DateMalformedStringException;

class User
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $email,
        private readonly string $password,
        private readonly ?DateTimeImmutable $createdAt = null,
    ) {
    }

    public static function create(string $name, string $email, string $password): self
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return new self(0, $name, $email, $hashedPassword);
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromPdoStd(stdClass $std): self
    {
        return new self(
            id: $std->id,
            name: $std->name,
            email: $std->email,
            password: $std->password,
            createdAt: !is_null($std->created_at) ? new DateTimeImmutable($std->created_at) : null,
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
