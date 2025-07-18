<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Service;

use DateMalformedStringException;
use TrafficTracker\Domain\Contract\AnalyticsStatsContract;
use TrafficTracker\Domain\DataTransferObject\AnalyticsStatsDto;
use TrafficTracker\Domain\DataTransferObject\DateRangeStatsDto;
use TrafficTracker\Domain\DataTransferObject\TopPageStatsDto;
use TrafficTracker\Domain\DataTransferObject\VisitorBreakDownCollectionDto;
use TrafficTracker\Domain\DataTransferObject\VisitorBreakDownStatsDto;
use TrafficTracker\Domain\Entity\DailyStats;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Entity\Visit;
use TrafficTracker\Domain\Enum\VisitorBreakDown;
use TrafficTracker\Domain\Repositories\VisitorRepositoryInterface;
use TrafficTracker\Domain\Repositories\VisitRepositoryInterface;
use TrafficTracker\Domain\Repositories\DailyStatsRepositoryInterface;
use DateTimeImmutable;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Application\Enum\Period;

readonly class AnalyticsService
{
    public function __construct(
        private VisitRepositoryInterface $visitRepository,
        private DailyStatsRepositoryInterface $dailyStatsRepository,
        private VisitorRepositoryInterface $visitorRepository,
    ) {
    }

    public function getDailyStats(Domain $domain, DateTimeImmutable $date): AnalyticsStatsContract
    {
        $stats = $this->dailyStatsRepository->findByDate($domain->getId(), $date);

        if (is_null($stats)) {
            return new AnalyticsStatsDto(
                date: $date,
                uniqueVisitors: 0,
                uniquePages: 0,
                totalVisits: 0,
            );
        }

        return new AnalyticsStatsDto(
            date: $stats->getDate(),
            uniqueVisitors: $stats->getUniqueVisitors(),
            uniquePages: $stats->getUniquePages(),
            totalVisits: $stats->getTotalVisits(),
        );
    }

    public function getByDateRange(Domain $domain, DatePeriod $datePeriod): AnalyticsStatsContract
    {
        /** @var array<int, DailyStats> $dailyStats */
        $dailyStats = $this->dailyStatsRepository->findByDateRange($domain->getId(), $datePeriod);

        $totalUniqueVisitors = 0;
        $totalVisits = 0;
        $totalUniquePages = 0;

        foreach ($dailyStats as $stats) {
            $totalUniqueVisitors += $stats->getUniqueVisitors();
            $totalVisits += $stats->getTotalVisits();
            $totalUniquePages += $stats->getUniquePages();
        }

        return new DateRangeStatsDto(
            datePeriod: $datePeriod,
            uniqueVisitors: $totalUniqueVisitors,
            uniquePages: $totalUniquePages,
            totalVisits: $totalVisits,
            dailyBreakdown: array_map(function ($stats) {
                return new AnalyticsStatsDto(
                    date: $stats->getDate(),
                    uniqueVisitors: $stats->getUniqueVisitors(),
                    uniquePages: $stats->getUniquePages(),
                    totalVisits: $stats->getTotalVisits(),
                );
            }, $dailyStats)
        );
    }

    /**
     * @return array<TopPageStatsDto>
     */
    public function getTopPages(
        Domain $domain,
        ?DatePeriod $datePeriod,
        int $limit = 10,
    ): array {
        $pages = $this->visitRepository->getPopularPages($domain->getId(), $limit, $datePeriod);

        return array_map(function ($page) {
            return new TopPageStatsDto(
                url: $page['url'],
                title: $page['page_title'],
                visits: $page['visits'],
                uniqueVisitors: $page['unique_visitors'],
            );
        }, $pages);
    }

    public function getVisitorBreakdown(Domain $domain, Period $period, ?DatePeriod $datePeriod = null): VisitorBreakDownCollectionDto
    {
        $effectiveDatePeriod = $datePeriod;

        if ($datePeriod === null) {
            $effectiveDatePeriod = $this->getDatePeriodForPeriod($period);
        }

        $browsers = $this->visitRepository->getBrowserStats($domain->getId(), $effectiveDatePeriod);
        $operatingSystems = $this->visitRepository->getOSStats($domain->getId(), $effectiveDatePeriod);
        $devices = $this->visitRepository->getDeviceStats($domain->getId(), $effectiveDatePeriod);

        return new VisitorBreakDownCollectionDto(
            $this->mapToVisitorBreakDown(VisitorBreakDown::BROWSER, $browsers),
            $this->mapToVisitorBreakDown(VisitorBreakDown::OS, $operatingSystems),
            $this->mapToVisitorBreakDown(VisitorBreakDown::DEVICE, $devices),
        );
    }

    public function getOverallVisitorBreakDown(Domain $domain): VisitorBreakDownCollectionDto
    {
        $browsers = $this->visitorRepository->getBrowserStats($domain->getId());
        $operatingSystems = $this->visitorRepository->getOSStats($domain->getId());
        $devices = $this->visitorRepository->getDeviceStats($domain->getId());

        return new VisitorBreakDownCollectionDto(
            $this->mapToVisitorBreakDown(VisitorBreakDown::BROWSER, $browsers),
            $this->mapToVisitorBreakDown(VisitorBreakDown::OS, $operatingSystems),
            $this->mapToVisitorBreakDown(VisitorBreakDown::DEVICE, $devices),
        );
    }

    private function mapToVisitorBreakDown(VisitorBreakDown $type, array $data): array
    {
        return array_map(fn (array $item) => new VisitorBreakDownStatsDto(
            $type,
            $item[$type->getPayloadKey()],
            $item['label'],
            $item['count'],
            $item['percentage']
        ), $data);
    }

    /**
     * @return array<Visit>
     *
     * @throws DateMalformedStringException
     */
    public function getRecentVisits(Domain $domain, int $limit = 20): array
    {
        $visits = $this->visitRepository->getRecentVisits($domain->getId(), $limit);

        return array_map(function (array $visit) {
            return Visit::fromPdoStd((object) $visit);
        }, $visits);
    }

    /**
     * @return array<DailyStats>
     */
    public function getHistoricalData(Domain $domain, DatePeriod $datePeriod): array
    {
        return $this->dailyStatsRepository->findByDateRange($domain->getId(), $datePeriod);
    }

    private function getDatePeriodForPeriod(Period $period): ?DatePeriod
    {
        $now = new DateTimeImmutable();

        return match ($period) {
            Period::DAILY => new DatePeriod($now, $now),
            Period::WEEKLY => new DatePeriod($now->modify('-7 days'), $now),
            Period::MONTHLY => new DatePeriod($now->modify('-30 days'), $now),
            default => null,
        };
    }
}
