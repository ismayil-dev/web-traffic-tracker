<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Repository;

use PDOStatement;
use TrafficTracker\Domain\Entity\Visit;
use TrafficTracker\Domain\Repositories\VisitRepositoryInterface;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Domain\ValueObject\VisitorHash;
use PDO;

class VisitRepository extends BaseRepository implements VisitRepositoryInterface
{
    public function save(Visit $visit): Visit
    {
        $sql = new SqlBuilder();
        $sql->append('INSERT INTO visits (domain_id, url, base_url, page_title, visitor_ip, user_agent, browser, os, device, visitor_hash, referrer) ');
        $sql->append('VALUES (:domain_id, :url, :base_url, :page_title, :visitor_ip, :user_agent, :browser, :os, :device, :visitor_hash, :referrer)');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $visit->getDomainId());
        $stmt->bindValue(':url', $visit->getUrl()->getValue());
        $stmt->bindValue(':base_url', $visit->getUrl()->getBase());
        $stmt->bindValue(':page_title', $visit->getPageTitle());
        $stmt->bindValue(':visitor_ip', $visit->getVisitorIp()->getValue());
        $stmt->bindValue(':user_agent', $visit->getUserAgent()->getValue());
        $stmt->bindValue(':browser', $visit->getBrowser()->value);
        $stmt->bindValue(':os', $visit->getOS()->value);
        $stmt->bindValue(':device', $visit->getDevice()->value);
        $stmt->bindValue(':visitor_hash', $visit->getVisitorHash()->getValue());
        $stmt->bindValue(':referrer', $visit->getReferrer());
        $stmt->execute();

        return $this->findById($this->lastInsertId());
    }

    public function findById(int $id): ?Visit
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM visits WHERE id = :id LIMIT 1');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return Visit::fromPdoStd($stmt->fetchObject());
    }

    public function findByDomain(int $domainId, ?DatePeriod $datePeriod = null): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM visits WHERE domain_id = :domain_id');
        $stmt = $this->applyDateFilter($sql, $datePeriod, $domainId);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUrl(int $domainId, string $baseUrl, ?DatePeriod $datePeriod = null): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM visits WHERE domain_id = :domain_id AND base_url = :base_url');
        $sql->when(!is_null($datePeriod), function ($query) {
            $query->append('AND timestamp BETWEEN :from AND :to');
        });

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->bindValue(':base_url', $baseUrl);

        if (!is_null($datePeriod)) {
            $period = $datePeriod->getAsIsoString();
            $stmt->bindValue(':from', $period['from']);
            $stmt->bindValue(':to', $period['to']);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByVisitor(int $domainId, VisitorHash $visitorHash): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM visits WHERE domain_id = :domain_id AND visitor_hash = :visitor_hash');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->bindValue(':visitor_hash', $visitorHash->getValue());
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByDomain(int $domainId, ?DatePeriod $datePeriod = null): int
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT COUNT(*) FROM visits WHERE domain_id = :domain_id');
        $stmt = $this->applyDateFilter($sql, $datePeriod, $domainId);

        return (int) $stmt->fetchColumn();
    }

    public function countUniqueVisitors(int $domainId, ?DatePeriod $datePeriod = null): int
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT COUNT(DISTINCT visitor_hash) FROM visits WHERE domain_id = :domain_id');
        $stmt = $this->applyDateFilter($sql, $datePeriod, $domainId);

        return (int) $stmt->fetchColumn();
    }

    public function countUniquePages(int $domainId, ?DatePeriod $datePeriod = null): int
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT COUNT(DISTINCT base_url) FROM visits WHERE domain_id = :domain_id');
        $stmt = $this->applyDateFilter($sql, $datePeriod, $domainId);

        return (int) $stmt->fetchColumn();
    }

    public function getPopularPages(int $domainId, int $limit = 10, ?DatePeriod $datePeriod = null): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT base_url as url, page_title, COUNT(*) as visits, COUNT(DISTINCT visitor_hash) as unique_visitors');
        $sql->append('FROM visits WHERE domain_id = :domain_id');

        $sql->when(!is_null($datePeriod), function ($query) {
            $query->append('AND timestamp BETWEEN :from AND :to');
        });

        $sql->append('GROUP BY base_url, page_title');
        $sql->append('ORDER BY visits DESC');
        $sql->append('LIMIT :limit');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        if (!is_null($datePeriod)) {
            $period = $datePeriod->getAsIsoString();
            $stmt->bindValue(':from', $period['from']);
            $stmt->bindValue(':to', $period['to']);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrowserStats(int $domainId, ?DatePeriod $datePeriod = null): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT browser as browser, browser as label, COUNT(DISTINCT visitor_hash) as count');
        $sql->append('FROM visits WHERE domain_id = :domain_id');

        $sql->when(!is_null($datePeriod), function ($query) {
            $query->append('AND timestamp BETWEEN :from AND :to');
        });

        $sql->append('GROUP BY browser ORDER BY count DESC');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);

        if (!is_null($datePeriod)) {
            $period = $datePeriod->getAsIsoString();
            $stmt->bindValue(':from', $period['from']);
            $stmt->bindValue(':to', $period['to']);
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = array_sum(array_column($results, 'count'));

        return array_map(function ($row) use ($total) {
            $row['percentage'] = $total > 0 ? round(($row['count'] / $total) * 100, 1) : 0.0;

            return $row;
        }, $results);
    }

    public function getOSStats(int $domainId, ?DatePeriod $datePeriod = null): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT os as os, os as label, COUNT(DISTINCT visitor_hash) as count');
        $sql->append('FROM visits WHERE domain_id = :domain_id');

        $sql->when(!is_null($datePeriod), function ($query) {
            $query->append('AND timestamp BETWEEN :from AND :to');
        });

        $sql->append('GROUP BY os ORDER BY count DESC');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);

        if (!is_null($datePeriod)) {
            $period = $datePeriod->getAsIsoString();
            $stmt->bindValue(':from', $period['from']);
            $stmt->bindValue(':to', $period['to']);
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = array_sum(array_column($results, 'count'));

        return array_map(function ($row) use ($total) {
            $row['percentage'] = $total > 0 ? round(($row['count'] / $total) * 100, 1) : 0.0;

            return $row;
        }, $results);
    }

    public function getDeviceStats(int $domainId, ?DatePeriod $datePeriod = null): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT device as device, device as label, COUNT(DISTINCT visitor_hash) as count');
        $sql->append('FROM visits WHERE domain_id = :domain_id');

        $sql->when(!is_null($datePeriod), function ($query) {
            $query->append('AND timestamp BETWEEN :from AND :to');
        });

        $sql->append('GROUP BY device ORDER BY count DESC');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);

        if (!is_null($datePeriod)) {
            $period = $datePeriod->getAsIsoString();
            $stmt->bindValue(':from', $period['from']);
            $stmt->bindValue(':to', $period['to']);
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = array_sum(array_column($results, 'count'));

        return array_map(function ($row) use ($total) {
            $row['percentage'] = $total > 0 ? round(($row['count'] / $total) * 100, 1) : 0.0;

            return $row;
        }, $results);
    }

    public function getRecentVisits(int $domainId, int $limit = 20): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT id, domain_id, url, page_title, visitor_ip, user_agent, browser, os, device, visitor_hash, timestamp, referrer');
        $sql->append('FROM visits WHERE domain_id = :domain_id');
        $sql->append('ORDER BY timestamp DESC');
        $sql->append('LIMIT :limit');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function applyDateFilter(SqlBuilder $sql, ?DatePeriod $datePeriod, int $domainId): PDOStatement
    {
        $sql->when(!is_null($datePeriod), function ($query) {
            $query->append('AND timestamp BETWEEN :from AND :to');
        });
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        if (!is_null($datePeriod)) {
            $period = $datePeriod->getAsIsoString();
            $stmt->bindValue(':from', $period['from']);
            $stmt->bindValue(':to', $period['to']);
        }
        $stmt->execute();

        return $stmt;
    }
}
