<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Entity;

use stdClass;
use TrafficTracker\Domain\Enum\Browser;
use TrafficTracker\Domain\Enum\Device;
use TrafficTracker\Domain\Enum\OperatingSystem;
use TrafficTracker\Domain\ValueObject\UserAgent;
use TrafficTracker\Domain\ValueObject\VisitorHash;
use TrafficTracker\Domain\ValueObject\Url;
use TrafficTracker\Domain\ValueObject\IpAddress;
use DateTimeImmutable;
use DateMalformedStringException;

class Visit
{
    private DateTimeImmutable $timestamp;

    public function __construct(
        private readonly int $id,
        private readonly int $domainId,
        private readonly Url $url,
        private readonly ?string $pageTitle,
        private readonly IpAddress $visitorIp,
        private readonly UserAgent $userAgent,
        private readonly Browser $browser,
        private readonly OperatingSystem $os,
        private readonly Device $device,
        private readonly VisitorHash $visitorHash,
        ?DateTimeImmutable $timestamp = null,
        private readonly ?string $referrer = null,
    ) {
        $this->timestamp = $timestamp ?? new DateTimeImmutable();
    }

    public static function create(
        int $domainId,
        Url $url,
        ?string $pageTitle,
        IpAddress $visitorIp,
        UserAgent $userAgent,
        ?string $referrer = null,
    ): self {
        $visitorHash = VisitorHash::fromIpAndUserAgent($visitorIp, $userAgent->getValue());

        return new self(
            id:0,
            domainId: $domainId,
            url: $url,
            pageTitle: $pageTitle,
            visitorIp: $visitorIp,
            userAgent: $userAgent,
            browser: $userAgent->getBrowser(),
            os: $userAgent->getOS(),
            device: $userAgent->getDevice(),
            visitorHash: $visitorHash,
            referrer: $referrer
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

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    public function getVisitorIp(): IpAddress
    {
        return $this->visitorIp;
    }

    public function getUserAgent(): UserAgent
    {
        return $this->userAgent;
    }

    public function getBrowser(): Browser
    {
        return $this->browser;
    }

    public function getOS(): OperatingSystem
    {
        return $this->os;
    }

    public function getDevice(): Device
    {
        return $this->device;
    }

    public function getVisitorHash(): VisitorHash
    {
        return $this->visitorHash;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getReferrer(): ?string
    {
        return $this->referrer;
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromPdoStd(stdClass $std): self
    {
        return new self(
            id: $std->id,
            domainId: $std->domain_id,
            url: new Url($std->url),
            pageTitle: $std->page_title,
            visitorIp: new IpAddress($std->visitor_ip),
            userAgent: new UserAgent(
                $std->user_agent,
                Browser::from($std->browser),
                OperatingSystem::from($std->os),
                Device::from($std->device)
            ),
            browser: Browser::from($std->browser),
            os: OperatingSystem::from($std->os),
            device: Device::from($std->device),
            visitorHash: new VisitorHash($std->visitor_hash),
            timestamp: new DateTimeImmutable($std->timestamp),
            referrer: $std->referrer,
        );
    }
}
