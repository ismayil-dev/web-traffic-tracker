<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Service;

use TrafficTracker\Domain\Entity\Visit;
use TrafficTracker\Domain\Entity\Visitor;
use TrafficTracker\Domain\Entity\DailyStats;
use TrafficTracker\Domain\Repositories\VisitRepositoryInterface;
use TrafficTracker\Domain\Repositories\VisitorRepositoryInterface;
use TrafficTracker\Domain\Repositories\DailyStatsRepositoryInterface;
use DateMalformedStringException;
use TrafficTracker\Domain\ValueObject\DatePeriod;

readonly class TrackingService
{
    public function __construct(
        private VisitRepositoryInterface $visitRepository,
        private VisitorRepositoryInterface $visitorRepository,
        private DailyStatsRepositoryInterface $dailyStatsRepository,
    ) {
    }

    /**
     * @throws DateMalformedStringException
     */
    public function trackVisit(Visit $visit): Visit
    {
        $isNewPage = $this->isNewPageToday($visit);

        $recordedVisit = $this->visitRepository->save($visit);

        $isNewVisitor = !$this->visitorRepository->exists(
            $visit->getDomainId(),
            $visit->getVisitorHash()
        );

        $this->createOrUpdateVisitorRecord($visit);

        $this->updateDailyStats($visit, $isNewPage, $isNewVisitor);

        return $recordedVisit;
    }

    private function createOrUpdateVisitorRecord(Visit $visit): void
    {
        $visitor = $this->visitorRepository->findByHash(
            $visit->getDomainId(),
            $visit->getVisitorHash()
        );

        if (is_null($visitor)) {
            $visitor = Visitor::createNew(
                $visit->getDomainId(),
                $visit->getVisitorHash(),
                $visit->getBrowser(),
                $visit->getOS(),
                $visit->getDevice()
            );
            $this->visitorRepository->save($visitor);

            return;
        }

        $visitor->recordVisit();
        $this->visitorRepository->update($visitor);
    }

    private function updateDailyStats(Visit $visit, bool $isNewPage, bool $isNewVisitor): void
    {
        $today = $visit->getTimestamp()->setTime(0, 0, 0, 0);

        $dailyStats = $this->dailyStatsRepository->findByDate(
            $visit->getDomainId(),
            $today
        );

        if (is_null($dailyStats)) {
            $dailyStats = DailyStats::createForDate($visit->getDomainId(), $today);
        }

        if ($dailyStats->getId() === 0) {
            $dailyStats->addVisit(true, true);
            $this->dailyStatsRepository->save($dailyStats);
        } else {
            $dailyStats->addVisit($isNewVisitor, $isNewPage);
            $this->dailyStatsRepository->update($dailyStats);
        }
    }

    /**
     * @throws DateMalformedStringException
     */
    private function isNewPageToday(Visit $visit): bool
    {
        $today = $visit->getTimestamp()->setTime(0, 0, 0, 0);
        $tomorrow = $today->modify('+1 day');

        $existingVisits = $this->visitRepository->findByUrl(
            $visit->getDomainId(),
            $visit->getUrl()->getBase(),
            new DatePeriod($today, $tomorrow)
        );

        return count($existingVisits) === 0;
    }

    public function isValidTrackingRequest(Visit $visit): bool
    {
        if (!$visit->getUrl()->isValidForTracking()) {
            return false;
        }

        return true;
    }
}
