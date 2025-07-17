<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Repository;

class SqlBuilder
{
    private string $query;

    public function __construct()
    {
        $this->query = '';
    }

    public function append(string $query): void
    {
        $this->query .= $query.PHP_EOL;
    }

    public function when($condition, callable $callback): void
    {
        if ($condition) {
            $callback($this);
        }
    }

    public function limit(int $limit): void
    {
        $this->query .= "LIMIT $limit".PHP_EOL;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
