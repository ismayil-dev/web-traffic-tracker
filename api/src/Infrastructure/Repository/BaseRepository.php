<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Repository;

use PDO;
use TrafficTracker\Infrastructure\Exception\DatabaseConnectionFailed;
use Throwable;

class BaseRepository
{
    protected PDO $db;

    /**
     * @throws DatabaseConnectionFailed
     */
    public function __construct()
    {
        $this->establishDatabaseConnection();
    }

    /**
     * @throws DatabaseConnectionFailed
     */
    private function establishDatabaseConnection(): void
    {
        try {
            $this->db = new PDO(
                sprintf(
                    '%s:host=%s;port=%d;dbname=%s',
                    $_ENV['DB_TYPE'],
                    $_ENV['DB_HOST'],
                    $_ENV['DB_PORT'],
                    $_ENV['DB_NAME']
                ),
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );

            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (Throwable) {
            throw new DatabaseConnectionFailed();
        }
    }

    public function lastInsertId(): int
    {
        return (int) $this->db->lastInsertId();
    }
}
