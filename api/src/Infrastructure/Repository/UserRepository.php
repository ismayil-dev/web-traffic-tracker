<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Repository;

use stdClass;
use TrafficTracker\Domain\Entity\User;
use TrafficTracker\Domain\Repositories\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $sql = new SqlBuilder();
        $sql->append('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt = $this->db->prepare($sql->getQuery());
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        /** @var stdClass|false $result */
        $result = $stmt->fetchObject();

        if ($result === false) {
            return null;
        }

        return User::fromPdoStd($result);
    }
}
