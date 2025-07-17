<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use TrafficTracker\Application\DataTransferObject\AnalyticsTopPages;
use TrafficTracker\Application\DataTransferObject\GetTopPagesRequest;
use TrafficTracker\Domain\Service\AnalyticsService;

readonly class GetTopPages
{
    use HasDatePeriod;

    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    public function execute(GetTopPagesRequest $request): AnalyticsTopPages
    {
        $datePeriod = $this->getDatePeriod($request->period, $request->datePeriod);

        $pages = $this->analyticsService->getTopPages(
            $request->domain,
            $datePeriod,
            $request->limit
        );

        return new AnalyticsTopPages($pages);
    }
}
