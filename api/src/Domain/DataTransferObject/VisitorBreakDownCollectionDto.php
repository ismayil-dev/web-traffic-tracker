<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\DataTransferObject;

class VisitorBreakDownCollectionDto
{
    /**
     * @param array<VisitorBreakDownStatsDto> $browsers
     * @param array<VisitorBreakDownStatsDto> $operatingSystems
     * @param array<VisitorBreakDownStatsDto> $devices
     */
    public function __construct(
        public array $browsers,
        public array $operatingSystems,
        public array $devices,
    ) {
    }

    public function toArray(): array
    {
        return [
            'browsers' => array_map(fn (VisitorBreakDownStatsDto $item) => $item->toArray(), $this->browsers),
            'operating_systems' => array_map(fn (VisitorBreakDownStatsDto $item) => $item->toArray(), $this->operatingSystems),
            'devices' => array_map(fn (VisitorBreakDownStatsDto $item) => $item->toArray(), $this->devices),
        ];
    }
}
