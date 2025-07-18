<?php declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Database;

use PDO;
use Throwable;
use TrafficTracker\Infrastructure\Exception\DatabaseConnectionFailed;

class DatabaseConnector
{
    private PDO $instance;

    /**
     * @throws DatabaseConnectionFailed
     */
    public function __construct()
    {
        try {
            $this->instance = new PDO(
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

            $this->instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (Throwable) {
            throw new DatabaseConnectionFailed();
        }
    }

    public function getInstance(): PDO
    {
        return $this->instance;
    }
}