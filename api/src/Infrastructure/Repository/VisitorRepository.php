<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Repository;

use PDO;
use TrafficTracker\Domain\Entity\Visitor;
use TrafficTracker\Domain\Repositories\VisitorRepositoryInterface;
use TrafficTracker\Domain\ValueObject\VisitorHash;
use stdClass;

class VisitorRepository extends BaseRepository implements VisitorRepositoryInterface
{
    public function save(Visitor $visitor): Visitor
    {
        $sql = new SqlBuilder();
        $sql->append('INSERT INTO unique_visitors (domain_id, visitor_hash, device, os, browser, first_visit, last_visit, total_visits) ');
        $sql->append('VALUES (:domain_id, :visitor_hash, :device, :os, :browser, :first_visit, :last_visit, :total_visits)');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $visitor->getDomainId());
        $stmt->bindValue(':visitor_hash', $visitor->getVisitorHash()->getValue());
        $stmt->bindValue(':device', $visitor->getDevice()->value);
        $stmt->bindValue(':os', $visitor->getOs()->value);
        $stmt->bindValue(':browser', $visitor->getBrowser()->value);
        $stmt->bindValue(':first_visit', $visitor->getFirstVisit()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':last_visit', $visitor->getLastVisit()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':total_visits', $visitor->getTotalVisits());
        $stmt->execute();

        return $this->findById($this->lastInsertId());
    }

    public function update(Visitor $visitor): Visitor
    {
        $sql = new SqlBuilder();
        $sql->append('UPDATE unique_visitors SET last_visit = :last_visit, total_visits = :total_visits ');
        $sql->append('WHERE domain_id = :domain_id AND visitor_hash = :visitor_hash');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $visitor->getDomainId());
        $stmt->bindValue(':visitor_hash', $visitor->getVisitorHash()->getValue());
        $stmt->bindValue(':last_visit', $visitor->getLastVisit()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':total_visits', $visitor->getTotalVisits());
        $stmt->execute();

        return $this->findById($visitor->getId());
    }

    public function findById(int $id): ?Visitor
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM unique_visitors WHERE id = :id LIMIT 1');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        /** @var stdClass|false $result */
        $result = $stmt->fetchObject();

        if ($result === false) {
            return null;
        }

        return Visitor::fromPdoStd($result);
    }

    public function findByHash(int $domainId, VisitorHash $visitorHash): ?Visitor
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM unique_visitors WHERE domain_id = :domain_id AND visitor_hash = :visitor_hash LIMIT 1');

        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->bindValue(':visitor_hash', $visitorHash->getValue());
        $stmt->execute();
        /** @var stdClass|false $result */
        $result = $stmt->fetchObject();

        if ($result === false) {
            return null;
        }

        return Visitor::fromPdoStd($result);
    }

    public function exists(int $domainId, VisitorHash $visitorHash): bool
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT EXISTS (SELECT 1 FROM unique_visitors WHERE domain_id = :domain_id AND visitor_hash = :visitor_hash LIMIT 1)');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->bindValue(':visitor_hash', $visitorHash->getValue());
        $stmt->execute();

        return $stmt->fetchColumn() === 1;
    }

    public function getBrowserStats(int $domainId): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT browser, browser as label, COUNT(*) as count FROM unique_visitors WHERE domain_id = :domain_id GROUP BY browser');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = array_sum(array_column($results, 'count'));

        return array_map(function ($row) use ($total) {
            $row['percentage'] = $total > 0 ? round(($row['count'] / $total) * 100, 1) : 0.0;

            return $row;
        }, $results);
    }

    public function getOSStats(int $domainId): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT os, os as label, COUNT(*) as count FROM unique_visitors WHERE domain_id = :domain_id GROUP BY os');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = array_sum(array_column($results, 'count'));

        return array_map(function ($row) use ($total) {
            $row['percentage'] = $total > 0 ? round(($row['count'] / $total) * 100, 1) : 0.0;

            return $row;
        }, $results);
    }

    public function getDeviceStats(int $domainId): array
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT device, device as label, COUNT(*) as count FROM unique_visitors WHERE domain_id = :domain_id GROUP BY device');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain_id', $domainId);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = array_sum(array_column($results, 'count'));

        return array_map(function ($row) use ($total) {
            $row['percentage'] = $total > 0 ? round(($row['count'] / $total) * 100, 1) : 0.0;

            return $row;
        }, $results);
    }
}
