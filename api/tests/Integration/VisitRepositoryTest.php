<?php

namespace TrafficTracker\Tests\Integration;

use DateTimeImmutable;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Entity\Visit;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Domain\ValueObject\IpAddress;
use TrafficTracker\Domain\ValueObject\Url;
use TrafficTracker\Domain\ValueObject\UserAgent;
use TrafficTracker\Infrastructure\Repository\VisitRepository;
use TrafficTracker\Tests\InteractWithDatabase;

class VisitRepositoryTest extends InteractWithDatabase
{
    private VisitRepository $repository;
    private Domain $testDomain;

    protected function setUp(): void
    {
        $this->repository = new VisitRepository();

        $this->testDomain = $this->createTestDomain();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestDatabase();
    }

    public function testSaveVisitPersistsAllData()
    {
        $visit = $this->createTestVisit();

        $savedVisit = $this->repository->save($visit);

        $this->assertNotNull($savedVisit);
        $this->assertNotEquals(0, $savedVisit->getId());
        $this->assertEquals($visit->getUrl()->getValue(), $savedVisit->getUrl()->getValue());
        $this->assertEquals($visit->getUserAgent()->getValue(), $savedVisit->getUserAgent()->getValue());
        $this->assertEquals($visit->getVisitorIp()->getValue(), $savedVisit->getVisitorIp()->getValue());
        $this->assertEquals($visit->getPageTitle(), $savedVisit->getPageTitle());
    }

    public function testFindByDomainReturnsCorrectVisits()
    {
        $visit1 = $this->createTestVisit('https://example.com/page1');
        $visit2 = $this->createTestVisit('https://example.com/page2');

        $this->repository->save($visit1);
        $this->repository->save($visit2);

        $visits = $this->repository->findByDomain($this->testDomain->getId());

        $this->assertCount(2, $visits);
        $this->assertEquals('https://example.com/page1', $visits[0]['url']);
        $this->assertEquals('https://example.com/page2', $visits[1]['url']);
    }

    public function testFindByDomainWithDatePeriod()
    {
        $oldVisit = $this->createTestVisit('https://example.com/old', new DateTimeImmutable('-10 days'));
        $recentVisit = $this->createTestVisit('https://example.com/recent', new DateTimeImmutable('-2 days'));
        $todayVisit = $this->createTestVisit('https://example.com/today', new DateTimeImmutable());

        $this->repository->save($oldVisit);
        $this->repository->save($recentVisit);
        $this->repository->save($todayVisit);

        $datePeriod = new DatePeriod(
            new DateTimeImmutable('-7 days'),
            new DateTimeImmutable('now')
        );
        $visits = $this->repository->findByDomain($this->testDomain->getId(), $datePeriod);

        $this->assertCount(2, $visits);
        $this->assertEquals('https://example.com/recent', $visits[0]['url']);
        $this->assertEquals('https://example.com/today', $visits[1]['url']);
    }

    public function testGetPopularPagesReturnsTopResults()
    {
        $this->repository->save($this->createTestVisit('https://example.com/page1'));
        $this->repository->save($this->createTestVisit('https://example.com/page1'));
        $this->repository->save($this->createTestVisit('https://example.com/page1'));
        $this->repository->save($this->createTestVisit('https://example.com/page2'));
        $this->repository->save($this->createTestVisit('https://example.com/page2'));
        $this->repository->save($this->createTestVisit('https://example.com/page3'));

        $popularPages = $this->repository->getPopularPages($this->testDomain->getId(), 3);

        $this->assertCount(3, $popularPages);
        $this->assertEquals('example.com/page1', $popularPages[0]['url']);
        $this->assertEquals(3, $popularPages[0]['visits']);
        $this->assertEquals('example.com/page2', $popularPages[1]['url']);
        $this->assertEquals(2, $popularPages[1]['visits']);
        $this->assertEquals('example.com/page3', $popularPages[2]['url']);
        $this->assertEquals(1, $popularPages[2]['visits']);
    }

    public function testGetBrowserStatsCalculatesPercentages()
    {
        $this->repository->save($this->createTestVisit('https://example.com/test', null, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'));
        $this->repository->save($this->createTestVisit('https://example.com/test', null, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0'));
        $this->repository->save($this->createTestVisit('https://example.com/test', null, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'));

        $browserStats = $this->repository->getBrowserStats($this->testDomain->getId());

        $this->assertCount(2, $browserStats);
        
        $chromeStats = array_find($browserStats, fn($stat) => $stat['browser'] === 'chrome');
        $this->assertEquals(1, $chromeStats['count']);
        $this->assertEquals(50.0, round($chromeStats['percentage'], 1));

        $firefoxStats = array_find($browserStats, fn($stat) => $stat['browser'] === 'firefox');
        $this->assertEquals(1, $firefoxStats['count']);
        $this->assertEquals(50.0, round($firefoxStats['percentage'], 1));
    }

    public function testGetOSStatsCalculatesPercentages()
    {
        $this->repository->save($this->createTestVisit('https://example.com/test', null, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'));
        $this->repository->save($this->createTestVisit('https://example.com/test', null, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15'));
        $this->repository->save($this->createTestVisit('https://example.com/test', null, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'));

        $osStats = $this->repository->getOSStats($this->testDomain->getId());

        $this->assertCount(2, $osStats);
        $windowsStats = array_find($osStats, fn($stat) => $stat['os'] === 'windows');
        $this->assertEquals(1, $windowsStats['count']);
        $this->assertEquals(50.0, round($windowsStats['percentage'], 1));

        $macStats = array_find($osStats, fn($stat) => $stat['os'] === 'macos');
        $this->assertEquals(1, $macStats['count']);
        $this->assertEquals(50.0, round($macStats['percentage'], 1));
    }

    public function testGetDeviceStatsCalculatesPercentages()
    {
        $this->repository->save($this->createTestVisit('https://example.com/test', null, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'));
        $this->repository->save($this->createTestVisit('https://example.com/test', null, 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15'));

        $deviceStats = $this->repository->getDeviceStats($this->testDomain->getId());

        $this->assertCount(2, $deviceStats);
        
        $desktopStats = array_find($deviceStats, fn($stat) => $stat['device'] === 'desktop');
        $this->assertEquals(1, $desktopStats['count']);
        $this->assertEquals(50.0, $desktopStats['percentage']);

        $mobileStats = array_find($deviceStats, fn($stat) => $stat['device'] === 'iphone');
        $this->assertEquals(1, $mobileStats['count']);
        $this->assertEquals(50.0, $mobileStats['percentage']);
    }

    public function testGetRecentVisitsReturnsLatestVisits()
    {
        $oldVisit = $this->createTestVisit('https://example.com/old', new DateTimeImmutable('-1 hour'));
        $recentVisit = $this->createTestVisit('https://example.com/recent', new DateTimeImmutable('-10 minutes'));
        $latestVisit = $this->createTestVisit('https://example.com/latest', new DateTimeImmutable());

        $this->repository->save($oldVisit);
        $this->repository->save($recentVisit);
        $this->repository->save($latestVisit);

        $recentVisits = $this->repository->getRecentVisits($this->testDomain->getId(), 2);

        $this->assertCount(2, $recentVisits);
        $this->assertEquals('https://example.com/latest', $recentVisits[0]['url']);
        $this->assertEquals('https://example.com/recent', $recentVisits[1]['url']);
    }

    public function testCountVisitsByDomain()
    {
        $this->repository->save($this->createTestVisit('https://example.com/page1'));
        $this->repository->save($this->createTestVisit('https://example.com/page2'));
        $this->repository->save($this->createTestVisit('https://example.com/page3'));

        $count = $this->repository->countByDomain($this->testDomain->getId());

        $this->assertEquals(3, $count);
    }

    private function createTestVisit(
        string $url = 'https://example.com/page',
        ?DateTimeImmutable $timestamp = null,
        string $userAgentString = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ): Visit {
        return Visit::create(
            $this->testDomain->getId(),
            new Url($url),
            'Test Page',
            new IpAddress('192.168.1.1'),
            new UserAgent($userAgentString),
            'https://google.com',
            $timestamp
        );
    }
}