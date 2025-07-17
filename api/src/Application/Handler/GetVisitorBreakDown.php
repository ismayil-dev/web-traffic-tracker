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
        return $this->analyticsService->getVisitorBreakdown(
            $request->domain,
            $request->period,
            $request->datePeriod
        );
    }
}
