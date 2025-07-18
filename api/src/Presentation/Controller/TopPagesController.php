<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use DateMalformedStringException;
use DateTimeImmutable;
use TrafficTracker\Application\DataTransferObject\GetTopPagesRequest;
use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Application\Handler\GetTopPages;
use TrafficTracker\Domain\Service\AnalyticsService;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Repository\DailyStatsRepository;
use TrafficTracker\Infrastructure\Repository\VisitorRepository;
use TrafficTracker\Infrastructure\Repository\VisitRepository;

readonly class TopPagesController
{
    use AnalyticsRequestValidator;

    private GetTopPages $getTopPages;

    public function __construct()
    {
        $this->getTopPages = new GetTopPages(
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

        $validationError = $this->validate($data);

        if (!empty($validationError)) {
            return Response::unProcessableContent($validationError);
        }

        $period = !empty($data['period']) ? Period::from($data['period']) : Period::DAILY;
        $limit = !empty($data['limit']) ? (int) $data['limit'] : 10;

        $datePeriod = null;
        if (!empty($data['from']) && !empty($data['to'])) {
            $datePeriod = new DatePeriod(
                new DateTimeImmutable($data['from']),
                new DateTimeImmutable($data['to'])
            );
        }

        $topPages = $this->getTopPages->execute(new GetTopPagesRequest(
            domain: RequestContext::getDomain(),
            period: $period,
            datePeriod: $datePeriod,
            limit: $limit
        ));

        return Response::success($topPages->toArray());
    }
}
