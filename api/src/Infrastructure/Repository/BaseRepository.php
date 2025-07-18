<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Repository;

use PDO;
use TrafficTracker\Infrastructure\Database\DatabaseConnector;

class BaseRepository
{
    protected PDO $db;

    public function __construct()
    {
        $this->establishDatabaseConnection();
    }

    private function establishDatabaseConnection(): void
    {
        $this->db = new DatabaseConnector()->getInstance();
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->lastInsertId();
    }
}
