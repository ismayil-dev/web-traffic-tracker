<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use TrafficTracker\Application\DataTransferObject\TrackVisitRequest;
use TrafficTracker\Application\DataTransferObject\VisitResult;
use TrafficTracker\Application\Exception\UrlDoesNotMatchWithDomain;
use TrafficTracker\Application\Exception\VisitRejected;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Entity\Visit;
use TrafficTracker\Domain\Service\TrackingService;
use TrafficTracker\Domain\ValueObject\IpAddress;
use TrafficTracker\Domain\ValueObject\Url;
use DateMalformedStringException;
use TrafficTracker\Domain\ValueObject\UserAgent;

readonly class TrackVisit
{
    public function __construct(private TrackingService $trackingService)
    {
    }

    /**
     * @throws UrlDoesNotMatchWithDomain
     * @throws VisitRejected
     * @throws DateMalformedStringException
     */
    public function execute(TrackVisitRequest $request, Domain $domain): VisitResult
    {
        $url = Url::fromString($request->url);
        $ipAddress = IpAddress::fromString($request->ipAddress);

        if (!$url->isSameDomain($domain->getDomain())) {
            throw new UrlDoesNotMatchWithDomain();
        }

        $visit = Visit::create(
            $domain->getId(),
            $url,
            $request->pageTitle,
            $ipAddress,
            new UserAgent($request->userAgent),
            $request->referrer
        );

        if (!$this->trackingService->isValidTrackingRequest($visit)) {
            throw new VisitRejected();
        }

        $visit = $this->trackingService->trackVisit($visit);

        return new VisitResult($visit);
    }
}
