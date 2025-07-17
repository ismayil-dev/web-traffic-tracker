<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Domain\Entity\Visit;

readonly class VisitResult
{
    public function __construct(private Visit $visit)
    {

    }

    public function toArray(): array
    {
        return [
            'visit_id' => $this->visit->getId(),
            'visitor_hash' => $this->visit->getVisitorHash()->getShort(),
            'timestamp' => $this->visit->getTimestamp()->format('Y-m-d H:i:s'),
        ];
    }
}
