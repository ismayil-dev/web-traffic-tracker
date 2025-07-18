<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use DateMalformedStringException;
use DateTimeImmutable;
use TrafficTracker\Application\DataTransferObject\GetAnalyticsRequest;
use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Application\Exception\DatePeriodIsRequired;
use TrafficTracker\Application\Handler\GetAnalytics;
use TrafficTracker\Domain\Service\AnalyticsService;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Repository\DailyStatsRepository;
use TrafficTracker\Infrastructure\Repository\VisitorRepository;
use TrafficTracker\Infrastructure\Repository\VisitRepository;

readonly class AnalyticsController
{
    use AnalyticsRequestValidator;

    private GetAnalytics $getAnalytics;

    public function __construct()
    {
        $this->getAnalytics = new GetAnalytics(
            analyticsService: new AnalyticsService(
                visitRepository: new VisitRepository(),
                dailyStatsRepository: new DailyStatsRepository(),
                visitorRepository: new VisitorRepository()
            )
        );
    }

    /**
     * @throws DateMalformedStringException
     * @throws DatePeriodIsRequired
     */
    public function __invoke(): Response
    {
        $data = $_GET;

        $validationError = $this->validate($data, true);

        if (!empty($validationError)) {
            return Response::unProcessableContent($validationError);
        }

        $datePeriod = null;

        if (!empty($data['from']) && !empty($data['to'])) {
            $datePeriod = new DatePeriod(
                new DateTimeImmutable($data['from']),
                new DateTimeImmutable($data['to'])
            );
        }

        $request = new GetAnalyticsRequest(Period::from($data['period']), $datePeriod);
        $response = $this->getAnalytics->execute($request, RequestContext::getDomain());

        return Response::success($response->toArray());
    }
}
