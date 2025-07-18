<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use DateMalformedStringException;
use DateTimeImmutable;
use TrafficTracker\Application\DataTransferObject\HistoricalDataRequest;
use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Application\Handler\GetHistoricalData;
use TrafficTracker\Domain\Service\AnalyticsService;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Repository\DailyStatsRepository;
use TrafficTracker\Infrastructure\Repository\VisitorRepository;
use TrafficTracker\Infrastructure\Repository\VisitRepository;

readonly class HistoricalController
{
    use AnalyticsRequestValidator;

    private AnalyticsService $analyticsService;

    private GetHistoricalData $getHistoricalData;

    public function __construct()
    {
        $this->getHistoricalData = new GetHistoricalData(
            analyticsService: new AnalyticsService(
                visitRepository: new VisitRepository(),
                dailyStatsRepository: new DailyStatsRepository(),
                visitorRepository: new VisitorRepository()
            )
        );
    }

    /**
     * @throws DateMalformedStringException
     */
    public function __invoke(): Response
    {
        $data = $_GET;

        $validationError = $this->validate($data, true);

        if (!empty($validationError)) {
            return Response::unProcessableContent($validationError);
        }

        $period = Period::from($data['period']);

        $datePeriod = null;
        if (!empty($data['from']) && !empty($data['to'])) {
            $datePeriod = new DatePeriod(
                new DateTimeImmutable($data['from']),
                new DateTimeImmutable($data['to'])
            );
        }

        $historicalData = $this->getHistoricalData->execute(new HistoricalDataRequest(
            domain: RequestContext::getDomain(),
            period: $period,
            datePeriod: $datePeriod
        ));

        return Response::success($historicalData->toArray());
    }
}
