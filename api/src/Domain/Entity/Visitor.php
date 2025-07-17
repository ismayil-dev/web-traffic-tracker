<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Entity;

use stdClass;
use TrafficTracker\Domain\Enum\Browser;
use TrafficTracker\Domain\Enum\Device;
use TrafficTracker\Domain\Enum\OperatingSystem;
use TrafficTracker\Domain\ValueObject\VisitorHash;
use DateTimeImmutable;
use DateMalformedStringException;

class Visitor
{
    public function __construct(
        private readonly int $id,
        private readonly int $domainId,
        private readonly VisitorHash $visitorHash,
        private readonly Browser $browser,
        private readonly OperatingSystem $os,
        private readonly Device $device,
        private readonly DateTimeImmutable $firstVisit,
        private DateTimeImmutable $lastVisit,
        private int $totalVisits,
    ) {
    }

    public static function createNew(
        int $domainId,
        VisitorHash $visitorHash,
        Browser $browser,
        OperatingSystem $os,
        Device $device,
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            0,
            $domainId,
            $visitorHash,
            $browser,
            $os,
            $device,
            $now,
            $now,
            1
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDomainId(): int
    {
        return $this->domainId;
    }

    public function getVisitorHash(): VisitorHash
    {
        return $this->visitorHash;
    }

    public function getFirstVisit(): DateTimeImmutable
    {
        return $this->firstVisit;
    }

    public function getLastVisit(): DateTimeImmutable
    {
        return $this->lastVisit;
    }

    public function getTotalVisits(): int
    {
        return $this->totalVisits;
    }

    public function getDevice(): Device
    {
        return $this->device;
    }

    public function getOS(): OperatingSystem
    {
        return $this->os;
    }

    public function getBrowser(): Browser
    {
        return $this->browser;
    }

    public function recordVisit(): void
    {
        $this->lastVisit = new DateTimeImmutable();
        ++$this->totalVisits;
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromPdoStd(stdClass $std): self
    {
        return new self(
            id: $std->id,
            domainId: $std->domain_id,
            visitorHash: new VisitorHash($std->visitor_hash),
            browser: Browser::from($std->browser),
            os: OperatingSystem::from($std->os),
            device: Device::from($std->device),
            firstVisit: new DateTimeImmutable($std->first_visit),
            lastVisit: new DateTimeImmutable($std->last_visit),
            totalVisits: $std->total_visits,
        );
    }
}
