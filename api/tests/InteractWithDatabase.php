<?php declare(strict_types=1);

namespace TrafficTracker\Tests;

use PHPUnit\Framework\TestCase;

class InteractWithDatabase extends TestCase
{
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupTestDatabase();
    }
}
