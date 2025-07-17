<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\ValueObject;

use InvalidArgumentException;

class VisitorHash
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Visitor hash cannot be empty');
        }

        if (strlen($value) !== 64) {
            throw new InvalidArgumentException('Visitor hash must be exactly 64 characters');
        }

        if (!preg_match('/^[a-f0-9]+$/', $value)) {
            throw new InvalidArgumentException('Visitor hash must contain only lowercase hexadecimal characters');
        }

        $this->value = $value;
    }

    public static function fromIpAndUserAgent(IpAddress $ipAddress, string $userAgent): self
    {
        $data = $ipAddress->getValue().'|'.$userAgent;
        $hash = hash('sha256', $data);

        return new self($hash);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(VisitorHash $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getShort(): string
    {
        return substr($this->value, 0, 12);
    }
}
