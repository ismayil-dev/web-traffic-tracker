<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use DateTimeImmutable;
use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Domain\ValueObject\DatePeriod;

trait HasDatePeriod
{
    public function getDatePeriod(Period $period, ?DatePeriod $datePeriod = null): ?DatePeriod
    {
        $effectiveDatePeriod = $datePeriod;

        if ($datePeriod === null) {
            $effectiveDatePeriod = $this->getDatePeriodForPeriod($period);
        }

        return $effectiveDatePeriod;
    }

    private function getDatePeriodForPeriod(Period $period): ?DatePeriod
    {
        $now = new DateTimeImmutable();

        return match ($period) {
            Period::DAILY => new DatePeriod($now, $now),
            Period::WEEKLY => new DatePeriod($now->modify('-7 days'), $now),
            Period::MONTHLY => new DatePeriod($now->modify('-30 days'), $now),
            default => null,
        };
    }
}
