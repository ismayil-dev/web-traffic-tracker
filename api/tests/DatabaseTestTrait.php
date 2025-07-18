<?php

namespace TrafficTracker\Tests;

use DateTimeImmutable;
use PDO;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\ValueObject\ApiKey;
use TrafficTracker\Infrastructure\Database\DatabaseConnector;

trait DatabaseTestTrait
{
    protected function cleanupTestDatabase(): void
    {
        $pdo = $this->createTestPDO();
        
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('TRUNCATE TABLE visits');
        $pdo->exec('TRUNCATE TABLE unique_visitors');
        $pdo->exec('TRUNCATE TABLE daily_stats');
        $pdo->exec('TRUNCATE TABLE domains');
        $pdo->exec('TRUNCATE TABLE users');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    protected function createTestPDO(): PDO
    {
        return new DatabaseConnector()->getInstance();
    }

    protected function createTestDomain(): Domain
    {
        $pdo = $this->createTestPDO();
        
        // Create test user first
        $userStmt = $pdo->prepare('INSERT INTO users (id, name, email, password) VALUES (?, ?, ?, ?)');
        $userStmt->execute([1, 'Test User', 'test@example.com', 'password']);
        
        // Create test domain
        $apiKeyData = ApiKey::generate();
        $domain = new Domain(
            1,
            1,
            'example.com',
            $apiKeyData['api_key'],
            new DateTimeImmutable()
        );

        $domainStmt = $pdo->prepare('INSERT INTO domains (id, user_id, domain, api_key, created_at) VALUES (?, ?, ?, ?, ?)');
        $domainStmt->execute([
            $domain->getId(),
            $domain->getUserId(),
            $domain->getDomain(),
            $domain->getApiKey()->getHashedValue(),
            $domain->getCreatedAt()->format('Y-m-d H:i:s')
        ]);

        return $domain;
    }
}