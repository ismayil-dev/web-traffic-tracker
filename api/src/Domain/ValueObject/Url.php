<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\ValueObject;

use InvalidArgumentException;

class Url
{
    private string $value;
    private array $parsed;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('URL cannot be empty');
        }

        $parsed = parse_url($value);
        if (!$parsed || !isset($parsed['host'])) {
            throw new InvalidArgumentException('Invalid URL format');
        }

        $this->value = $value;
        $this->parsed = $parsed;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getScheme(): string
    {
        return $this->parsed['scheme'] ?? 'https';
    }

    public function getHost(): string
    {
        return $this->parsed['host'];
    }

    public function getPath(): string
    {
        return $this->parsed['path'] ?? '/';
    }

    public function getBase(): string
    {
        return  $this->parsed['host'].$this->parsed['path'];
    }

    public function isSameDomain(string $domain): bool
    {
        return $this->getHost() === $domain;
    }

    public function equals(Url $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isValidForTracking(): bool
    {
        return in_array($this->getScheme(), ['http', 'https'], true);
    }
}
