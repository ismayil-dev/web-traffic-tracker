<?php

namespace TrafficTracker\Tests\Feature;

use DateTimeImmutable;
use TrafficTracker\Application\Exception\UrlDoesNotMatchWithDomain;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Entity\User;
use TrafficTracker\Domain\ValueObject\ApiKey;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Presentation\Controller\TrackVisitController;
use TrafficTracker\Tests\InteractWithDatabase;

class TrackVisitControllerTest extends InteractWithDatabase
{
    private TrackVisitController $controller;
    private Domain $testDomain;
    private string $plainApiKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TrackVisitController();
        
        $apiKeyData = ApiKey::generate();
        $this->plainApiKey = $apiKeyData['plain'];
        
        $pdo = $this->createTestPDO();
        
        // Create test user
        $this->testUser = new User(
            id: 1,
            name: 'Test User',
            email: 'test@example.com',
            password: 'password',
            createdAt: new DateTimeImmutable()
        );
        $userStmt = $pdo->prepare('INSERT INTO users (id, name, email, password) VALUES (?, ?, ?, ?)');
        $userStmt->execute([
            $this->testUser->getId(),
            $this->testUser->getName(),
            $this->testUser->getEmail(),
            'password'
        ]);
        
        // Create test domain
        $this->testDomain = new Domain(
            1,
            1,
            'example.com',
            $apiKeyData['api_key'],
            new DateTimeImmutable()
        );
        RequestContext::setDomain($this->testDomain);
        RequestContext::setUser($this->testUser);

        $domainStmt = $pdo->prepare('INSERT INTO domains (id, user_id, domain, api_key, created_at) VALUES (?, ?, ?, ?, ?)');
        $domainStmt->execute([
            $this->testDomain->getId(),
            $this->testDomain->getUserId(),
            $this->testDomain->getDomain(),
            $this->testDomain->getApiKey()->getHashedValue(),
            $this->testDomain->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function testTrackVisitWithValidDataReturnsSuccess()
    {
        $this->setupHttpRequest();

        $requestData = [
            'url' => 'https://example.com/test-page',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'page_title' => 'Test Page',
            'referrer' => 'https://google.com'
        ];

        $this->mockPhpInput(json_encode($requestData));

        $response = $this->controller->__invoke();
        $result = $response->toArray();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(201, $result['statusCode']);
    }

    public function testTrackVisitWithInvalidUrlReturnsError()
    {
        $this->setupHttpRequest();

        $requestData = [
            'url' => 'not-a-valid-url',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'page_title' => 'Test Page'
        ];

        $this->mockPhpInput(json_encode($requestData));
        $response = $this->controller->__invoke();
        $result = $response->toArray();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(422, $result['statusCode']);
        $this->assertEquals('Invalid URL format', $result['data']['messages'][0]);
    }

    public function testTrackVisitWithDomainMismatchReturnsError()
    {
        $this->setupHttpRequest();

        $requestData = [
            'url' => 'https://different-domain.com/page',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'page_title' => 'Test Page'
        ];

        $this->expectException(UrlDoesNotMatchWithDomain::class);

        $this->mockPhpInput(json_encode($requestData));
        $this->controller->__invoke();
    }

    public function testTrackVisitWithInvalidJsonReturnsError()
    {
        $this->setupHttpRequest();

        $this->mockPhpInput('invalid-json-data');

        $response = $this->controller->__invoke();
        $result = $response->toArray();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(422, $result['statusCode']);
        $this->assertEquals('URL is required', $result['data']['messages'][0]);
        $this->assertEquals('User agent is required', $result['data']['messages'][1]);
    }

    private function setupHttpRequest(bool $includeApiKey = true): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/v1/track';
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['X-Domain-Id'] = '1';
        
        if ($includeApiKey) {
            $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $this->plainApiKey;
        }
    }

    private function mockPhpInput(string $data): void
    {
        $_POST = json_decode($data, true) ?: [];
    }
}