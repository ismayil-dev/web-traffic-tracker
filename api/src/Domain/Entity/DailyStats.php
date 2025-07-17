<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Entity;

use DateTimeImmutable;
use DateMalformedStringException;
use stdClass;

class DailyStats
{
    public function __construct(
        private readonly int $id,
        private readonly int $domainId,
        private readonly DateTimeImmutable $date,
        private int $uniqueVisitors = 0,
        private int $totalVisits = 0,
        private int $uniquePages = 0,
    ) {
    }

    public static function createForDate(int $domainId, DateTimeImmutable $date): self
    {
        return new self(0, $domainId, $date);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDomainId(): int
    {
        return $this->domainId;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getUniqueVisitors(): int
    {
        return $this->uniqueVisitors;
    }

    public function getTotalVisits(): int
    {
        return $this->totalVisits;
    }

    public function getUniquePages(): int
    {
        return $this->uniquePages;
    }

    public function addVisit(bool $isUniqueVisitor = false, bool $isUniquePage = false): void
    {
        ++$this->totalVisits;

        if ($isUniqueVisitor) {
            ++$this->uniqueVisitors;
        }

        if ($isUniquePage) {
            ++$this->uniquePages;
        }
    }

    public function isEmpty(): bool
    {
        return $this->totalVisits === 0;
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromPdoStd(stdClass $std): self
    {
        return new self(
            id: $std->id,
            domainId: $std->domain_id,
            date: new DateTimeImmutable($std->date),
            uniqueVisitors: $std->unique_visitors,
            totalVisits: $std->total_visits,
            uniquePages: $std->unique_pages,
        );
    }
}
