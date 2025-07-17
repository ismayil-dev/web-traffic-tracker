<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use DateTimeImmutable;
use TrafficTracker\Application\DataTransferObject\AnalyticsStatsWithPeriod;
use TrafficTracker\Application\DataTransferObject\GetAnalyticsRequest;
use TrafficTracker\Application\Enum\Period;
use TrafficTracker\Application\Exception\DatePeriodIsRequired;
use TrafficTracker\Domain\Contract\AnalyticsStatsContract;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Service\AnalyticsService;
use TrafficTracker\Domain\ValueObject\DatePeriod;

readonly class GetAnalytics
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    /**
     * @throws DatePeriodIsRequired
     */
    public function execute(GetAnalyticsRequest $request, Domain $domain): AnalyticsStatsWithPeriod
    {
        $stats = match ($request->period) {
            Period::DAILY => $this->getDailyAnalytics($domain),
            Period::WEEKLY => $this->getWeeklyAnalytics($domain),
            Period::MONTHLY => $this->getMonthlyAnalytics($domain),
            Period::CUSTOM => $this->getCustomAnalytics($domain, $request),
        };

        return new AnalyticsStatsWithPeriod(
            period: $request->period,
            stats: $stats,
        );
    }

    private function getDailyAnalytics(Domain $domain): AnalyticsStatsContract
    {
        $today = new DateTimeImmutable('today');

        return $this->analyticsService->getDailyStats($domain, $today);
    }

    private function getWeeklyAnalytics(Domain $domain): AnalyticsStatsContract
    {
        $weekStart = new DateTimeImmutable('monday this week');
        $weekEnd = $weekStart->modify('+7 days');

        return $this->analyticsService->getByDateRange($domain, new DatePeriod($weekStart, $weekEnd));
    }

    private function getMonthlyAnalytics(Domain $domain): AnalyticsStatsContract
    {
        $date = new DateTimeImmutable();
        $datePeriod = new DatePeriod(
            from: new DateTimeImmutable($date->format('Y-m-01')),
            to: new DateTimeImmutable($date->format('Y-m-t'))
        );

        return $this->analyticsService->getByDateRange($domain, $datePeriod);
    }

    /**
     * @throws DatePeriodIsRequired
     */
    public function getCustomAnalytics(Domain $domain, GetAnalyticsRequest $request): AnalyticsStatsContract
    {
        if (is_null($request->datePeriod)) {
            throw new DatePeriodIsRequired();
        }

        return $this->analyticsService->getByDateRange($domain, $request->datePeriod);
    }
}
