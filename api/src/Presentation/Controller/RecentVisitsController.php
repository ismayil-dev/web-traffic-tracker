<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use TrafficTracker\Application\DataTransferObject\RecentVisitsRequest;
use TrafficTracker\Application\Handler\GetRecentVisits;
use TrafficTracker\Domain\Service\AnalyticsService;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Repository\DailyStatsRepository;
use TrafficTracker\Infrastructure\Repository\VisitorRepository;
use TrafficTracker\Infrastructure\Repository\VisitRepository;

readonly class RecentVisitsController
{
    private GetRecentVisits $getRecentVisits;

    public function __construct()
    {
        $this->getRecentVisits = new GetRecentVisits(
            analyticsService: new AnalyticsService(
                visitRepository: new VisitRepository(),
                dailyStatsRepository: new DailyStatsRepository(),
                visitorRepository: new VisitorRepository()
            )
        );
    }

    public function __invoke(): Response
    {
        $data = $_GET;

        $validationError = $this->validateHttpRequest($data);

        if (!empty($validationError)) {
            return Response::unProcessableContent($validationError);
        }

        $limit = !empty($data['limit']) ? (int) $data['limit'] : 20;

        $recentVisits = $this->getRecentVisits->execute(new RecentVisitsRequest(
            RequestContext::getDomain(),
            $limit
        ));

        return Response::success($recentVisits->toArray());
    }

    private function validateHttpRequest(array $data): array
    {
        $errors = [];

        if (!empty($data['limit']) && (!is_numeric($data['limit']) || (int) $data['limit'] < 1)) {
            $errors[] = 'Limit must be a positive integer';
        }

        return $errors;
    }
}
