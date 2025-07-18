<?php declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use TrafficTracker\Application\Handler\GetAnalytics;
use TrafficTracker\Application\Handler\GetOverallAnalytics;
use TrafficTracker\Domain\Service\AnalyticsService;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Repository\DailyStatsRepository;
use TrafficTracker\Infrastructure\Repository\VisitorRepository;
use TrafficTracker\Infrastructure\Repository\VisitRepository;

class OverallAnalyticsController
{
    private GetOverallAnalytics $getOverallAnalytics;

    public function __construct()
    {
        $this->getOverallAnalytics = new GetOverallAnalytics(
            analyticsService: new AnalyticsService(
                visitRepository: new VisitRepository(),
                dailyStatsRepository: new DailyStatsRepository(),
                visitorRepository: new VisitorRepository()
            )
        );
    }

    public function __invoke(): Response
    {
        $result = $this->getOverallAnalytics->execute(RequestContext::getDomain());

        return Response::success($result->toArray());
    }
}