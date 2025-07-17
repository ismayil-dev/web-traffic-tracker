<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\ValueObject;

use InvalidArgumentException;

class IpAddress
{
    private string $value;
    private int $version;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('IP address cannot be empty');
        }

        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('Invalid IP address format');
        }

        $this->value = $value;
        $this->version = filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? 4 : 6;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function isIPv4(): bool
    {
        return $this->version === 4;
    }

    public function isIPv6(): bool
    {
        return $this->version === 6;
    }

    public function isPrivate(): bool
    {
        return !filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
    }

    public function isLoopback(): bool
    {
        return false === filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE);
    }

    public function isPublic(): bool
    {
        return !$this->isPrivate() && !$this->isLoopback();
    }

    public function equals(IpAddress $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getAnonymized(): string
    {
        if ($this->isIPv4()) {
            $parts = explode('.', $this->value);
            $parts[3] = '0';

            return implode('.', $parts);
        }

        // For IPv6, anonymize the last 64 bits
        $parts = explode(':', $this->value);
        for ($i = 4; $i < count($parts); ++$i) {
            $parts[$i] = '0';
        }

        return implode(':', $parts);
    }
}
