<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Repository;

use DateTimeImmutable;
use PDO;
use stdClass;
use TrafficTracker\Domain\Entity\DailyStats;
use TrafficTracker\Domain\Repositories\DailyStatsRepositoryInterface;
use TrafficTracker\Domain\ValueObject\DatePeriod;

class DailyStatsRepository extends BaseRepository implements DailyStatsRepositoryInterface
{
    public function save(DailyStats $stats): DailyStats
    {
        $sql = new SqlBuilder();
        $sql->append('INSERT INTO daily_stats (domain_id, date, unique_visitors, total_visits, unique_pages) ');
        $sql->append('VALUES (:domain_id, :date, :unique_visitors, :total_visits, :unique_pages)');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $stats->getDomainId());
        $stmt->bindValue(':date', $stats->getDate()->format('Y-m-d'));
        $stmt->bindValue(':unique_visitors', $stats->getUniqueVisitors());
        $stmt->bindValue(':total_visits', $stats->getTotalVisits());
        $stmt->bindValue(':unique_pages', $stats->getUniquePages());
        $stmt->execute();

        return $this->findById($this->lastInsertId());
    }

    public function update(DailyStats $stats): DailyStats
    {
        $sql = new SqlBuilder();
        $sql->append('UPDATE daily_stats SET unique_visitors = :unique_visitors, total_visits = :total_visits, unique_pages = :unique_pages WHERE id = :id');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':id', $stats->getId());
        $stmt->bindValue(':unique_visitors', $stats->getUniqueVisitors());
        $stmt->bindValue(':total_visits', $stats->getTotalVisits());
        $stmt->bindValue(':unique_pages', $stats->getUniquePages());
        $stmt->execute();

        return $this->findById($stats->getId());
    }

    public function findById(int $id): ?DailyStats
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM daily_stats WHERE id = :id LIMIT 1');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        /** @var stdClass|false $result */
        $result = $stmt->fetchObject();

        if ($result === false) {
            return null;
        }

        return DailyStats::fromPdoStd($result);
    }

    public function findByDate(int $domainId, DateTimeImmutable $date): ?DailyStats
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM daily_stats WHERE domain_id = :domain_id AND date = :date LIMIT 1');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->bindValue(':date', $date->format('Y-m-d'));
        $stmt->execute();
        /** @var stdClass|false $result */
        $result = $stmt->fetchObject();

        if ($result === false) {
            return null;
        }

        return DailyStats::fromPdoStd($result);
    }

    public function findByDateRange(int $domainId, DatePeriod $datePeriod): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM daily_stats WHERE domain_id = :domain_id AND date BETWEEN :from AND :to');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->bindValue(':from', $datePeriod->from->format('Y-m-d'));
        $stmt->bindValue(':to', $datePeriod->to->format('Y-m-d'));
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return DailyStats::fromPdoStd((object) $item);
        }, $data);
    }
}
