<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\ValueObject;

use Random\RandomException;

readonly class ApiKey
{
    public function __construct(private string $hashedValue)
    {
    }

    /**
     * @throws RandomException
     */
    public static function generate(): array
    {
        $plainValue = bin2hex(random_bytes(64));
        $hashedValue = hash('sha256', $plainValue);

        return [
            'plain' => $plainValue,
            'api_key' => new self($hashedValue),
        ];
    }

    public static function fromPlainValue(string $plainValue): self
    {
        $hashedValue = hash('sha256', $plainValue);

        return new self($hashedValue);
    }

    public static function fromHash(string $hashedValue): self
    {
        return new self($hashedValue);
    }

    public function getHashedValue(): string
    {
        return $this->hashedValue;
    }

    public function equals(ApiKey $other): bool
    {
        return $this->hashedValue === $other->hashedValue;
    }

    public function __toString(): string
    {
        return $this->hashedValue;
    }

    public function getMasked(): string
    {
        return substr($this->hashedValue, 0, 8).'...'.substr($this->hashedValue, -8);
    }
}
