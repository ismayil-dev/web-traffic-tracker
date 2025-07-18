<?php declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Service\AnalyticsService;

class GetOverallAnalytics
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    public function execute(Domain $domain)
    {
        return $this->analyticsService->getOverallAnalytics($domain);
    }
}