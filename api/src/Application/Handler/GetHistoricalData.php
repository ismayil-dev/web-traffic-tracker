<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use TrafficTracker\Application\DataTransferObject\HistoricalData;
use TrafficTracker\Application\DataTransferObject\HistoricalDataRequest;
use TrafficTracker\Domain\Service\AnalyticsService;

readonly class GetHistoricalData
{
    use HasDatePeriod;

    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    public function execute(HistoricalDataRequest $request): HistoricalData
    {
        $datePeriod = $this->getDatePeriod($request->period, $request->datePeriod);

        if (is_null($datePeriod)) {
            return new HistoricalData([]);
        }

        $stats = $this->analyticsService->getHistoricalData($request->domain, $datePeriod);

        return new HistoricalData($stats);
    }
}
