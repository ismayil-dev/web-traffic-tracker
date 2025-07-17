<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\ValueObject;

use DateTimeImmutable;

readonly class DatePeriod
{
    public function __construct(
        public DateTimeImmutable $from,
        public DateTimeImmutable $to,
    ) {

    }

    /**
     * @return array{from: string, to: string}
     */
    public function getAsIsoString($resetTime = true): array
    {
        $from = $this->from;
        $to = $this->to;

        if ($resetTime) {
            $from = $from->setTime(0, 0, 0, 0);
            $to = $to->setTime(23, 59, 59, 0);
        }

        return [
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
        ];
    }
}
