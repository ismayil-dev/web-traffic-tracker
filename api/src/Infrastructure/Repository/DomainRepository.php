<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Repository;

use stdClass;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Repositories\DomainRepositoryInterface;
use TrafficTracker\Domain\ValueObject\ApiKey;

class DomainRepository extends BaseRepository implements DomainRepositoryInterface
{
    public function save(Domain $domain): Domain
    {
        $sql = new SqlBuilder();
        $sql->append('INSERT INTO domains (user_id, domain, api_key) VALUES (:user_id, :domain, :api_key)');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':user_id', $domain->getUserId());
        $stmt->bindValue(':domain', $domain->getDomain());
        $stmt->bindValue(':api_key', $domain->getApiKey()->getHashedValue());
        $stmt->execute();

        return $this->findByDomain($domain->getDomain());
    }

    public function findById(int $id): ?Domain
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM domains WHERE id = :id LIMIT 1');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        /** @var stdClass|false $result */
        $result = $stmt->fetchObject();

        if ($result === false) {
            return null;
        }

        return Domain::fromPdoStd($result);
    }

    public function findByDomain(string $domain): ?Domain
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM domains WHERE domain = :domain LIMIT 1');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain', $domain);
        $stmt->execute();
        /** @var stdClass|false $result */
        $result = $stmt->fetchObject();

        if ($result === false) {
            return null;
        }


        return Domain::fromPdoStd($result);
    }

    public function findByApiKey(ApiKey $apiKey): ?Domain
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM domains WHERE api_key = :api_key LIMIT 1');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':api_key', $apiKey->getHashedValue());
        $stmt->execute();
        /** @var stdClass|false $result */
        $result = $stmt->fetchObject();

        if ($result === false) {
            return null;
        }

        return Domain::fromPdoStd($result);
    }

    public function findAll(): array
    {
        // TODO: Implement findAll() method.
    }

    public function exists(string $domain): bool
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT EXISTS (SELECT 1 FROM domains WHERE domain = :domain LIMIT 1)');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':domain', $domain);
        $stmt->execute();

        return $stmt->fetchColumn() === 1;
    }
}
