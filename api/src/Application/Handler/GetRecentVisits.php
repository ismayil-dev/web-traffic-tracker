<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use DateMalformedStringException;
use TrafficTracker\Application\DataTransferObject\RecentVisits;
use TrafficTracker\Application\DataTransferObject\RecentVisitsRequest;
use TrafficTracker\Domain\Service\AnalyticsService;

readonly class GetRecentVisits
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    /**
     * @throws DateMalformedStringException
     */
    public function execute(RecentVisitsRequest $request): RecentVisits
    {
        $visits = $this->analyticsService->getRecentVisits($request->domain, $request->limit);

        return new RecentVisits($visits);
    }
}
