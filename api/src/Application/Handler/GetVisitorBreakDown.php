<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use TrafficTracker\Application\DataTransferObject\VisitorBreakDownRequest;
use TrafficTracker\Domain\DataTransferObject\VisitorBreakDownCollectionDto;
use TrafficTracker\Domain\Service\AnalyticsService;

class GetVisitorBreakDown
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    public function execute(VisitorBreakDownRequest $request): VisitorBreakDownCollectionDto
    {
        if (is_null($request->datePeriod) && is_null($request->period)) {
            return $this->analyticsService->getOverallVisitorBreakDown($request->domain);
        }

        return $this->analyticsService->getVisitorBreakdown(
            $request->domain,
            $request->period,
            $request->datePeriod
        );
    }
}
