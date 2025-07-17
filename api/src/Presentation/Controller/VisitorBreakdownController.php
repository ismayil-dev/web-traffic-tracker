<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use DateMalformedStringException;
use DateTimeImmutable;
use TrafficTracker\Application\DataTransferObject\VisitorBreakDownRequest;
use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Application\Handler\GetVisitorBreakDown;
use TrafficTracker\Domain\Service\AnalyticsService;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Repository\DailyStatsRepository;
use TrafficTracker\Infrastructure\Repository\VisitRepository;

readonly class VisitorBreakdownController
{
    use AnalyticsRequestValidator;

    private GetVisitorBreakDown $getVisitorBreakDown;

    public function __construct()
    {
        $this->getVisitorBreakDown = new GetVisitorBreakDown(
            analyticsService: new AnalyticsService(
                visitRepository: new VisitRepository(),
                dailyStatsRepository: new DailyStatsRepository()
            )
        );
    }

    /**
     * @throws DateMalformedStringException
     */
    public function __invoke(): Response
    {
        $data = $_GET;

        $validationError = $this->validate($data);

        if (!empty($validationError)) {
            return Response::unProcessableContent($validationError);
        }

        $period = !empty($data['period']) ? Period::from($data['period']) : Period::DAILY;

        $datePeriod = null;
        if (!empty($data['from']) && !empty($data['to'])) {
            $datePeriod = new DatePeriod(
                new DateTimeImmutable($data['from']),
                new DateTimeImmutable($data['to'])
            );
        }

        $breakdowns = $this->getVisitorBreakDown->execute(new VisitorBreakDownRequest(
            RequestContext::getDomain(),
            $period,
            $datePeriod
        ));

        return Response::success($breakdowns->toArray());
    }
}
