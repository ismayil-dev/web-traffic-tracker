<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\DataTransferObject;

use TrafficTracker\Domain\Enum\VisitorBreakDown;

readonly class VisitorBreakDownStatsDto
{
    public function __construct(
        public VisitorBreakDown $type,
        public string $typeValue,
        public string $label,
        public int $count,
        public float $percentage,
    ) {
    }

    public function toArray(): array
    {
        return [
            $this->type->getPayloadKey() => $this->typeValue,
            'label' => $this->label,
            'count' => $this->count,
            'percentage' => $this->percentage,
        ];
    }
}
