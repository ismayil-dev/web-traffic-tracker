<?php

namespace Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TrafficTracker\Domain\Enum\Browser;
use TrafficTracker\Domain\Enum\Device;
use TrafficTracker\Domain\Enum\OperatingSystem;
use TrafficTracker\Domain\Service\TrackingService;
use TrafficTracker\Domain\Repositories\VisitRepositoryInterface;
use TrafficTracker\Domain\Repositories\VisitorRepositoryInterface;
use TrafficTracker\Domain\Repositories\DailyStatsRepositoryInterface;
use TrafficTracker\Domain\Entity\Visit;
use TrafficTracker\Domain\Entity\Visitor;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\ValueObject\ApiKey;
use TrafficTracker\Domain\ValueObject\DatePeriod;
use TrafficTracker\Domain\ValueObject\Url;
use TrafficTracker\Domain\ValueObject\UserAgent;
use TrafficTracker\Domain\ValueObject\IpAddress;
use TrafficTracker\Domain\ValueObject\VisitorHash;
use DateTime;

class TrackingServiceTest extends TestCase
{
    private TrackingService $trackingService;
    private MockObject|VisitRepositoryInterface $visitRepository;
    private MockObject|VisitorRepositoryInterface $visitorRepository;
    private MockObject|DailyStatsRepositoryInterface $dailyStatsRepository;

    protected function setUp(): void
    {
        $this->visitRepository = $this->createMock(VisitRepositoryInterface::class);
        $this->visitorRepository = $this->createMock(VisitorRepositoryInterface::class);
        $this->dailyStatsRepository = $this->createMock(DailyStatsRepositoryInterface::class);

        $this->trackingService = new TrackingService(
            $this->visitRepository,
            $this->visitorRepository,
            $this->dailyStatsRepository,
        );
    }

    public function testTrackVisitCreatesNewVisitor()
    {
        $domain = $this->createMockDomain();
        $url = new Url('https://example.com/page');
        $userAgent = new UserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $ipAddress = new IpAddress('192.168.1.1');
        $visit = Visit::create(
            domainId: $domain->getId(),
            url: $url,
            pageTitle: 'Test Page',
            visitorIp: $ipAddress,
            userAgent: $userAgent,
            referrer: 'https://google.com',
        );

        $this->visitorRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Visitor::class));

        $this->visitRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Visit::class))
            ->willReturn($visit);

        $result = $this->trackingService->trackVisit($visit);

        $this->assertInstanceOf(Visit::class, $result);
        $this->assertEquals($userAgent->getValue(), $result->getUserAgent()->getValue());
        $this->assertEquals($ipAddress->getValue(), $result->getVisitorIp()->getValue());
    }

    public function testTrackVisitUpdatesExistingVisitor()
    {
        $domain = $this->createMockDomain();
        $url = new Url('https://example.com/new-page');
        $userAgent = new UserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $browser = Browser::CHROME;
        $os = OperatingSystem::WINDOWS;
        $device = Device::DESKTOP;
        $ipAddress = new IpAddress('192.168.1.1');
        $visitTime = new DateTimeImmutable()->setTime(0, 0, 0, 0);
        $tomorrow = new DateTimeImmutable('tomorrow');
        $datePeriod = new DatePeriod($visitTime, $tomorrow);
        $visitorHash = VisitorHash::fromIpAndUserAgent($ipAddress, $userAgent);
        $existingVisitor = Visitor::createNew(
            $domain->getId(),
            $visitorHash,
            browser: $browser,
            os: $os,
            device: $device
        );
        $visit = Visit::create(
            domainId: $domain->getId(),
            url: $url,
            pageTitle: 'Test Page',
            visitorIp: $ipAddress,
            userAgent: $userAgent,
            referrer: 'https://google.com',
            timestamp: $visitTime,
        );

        $this->visitRepository
            ->expects($this->once())
            ->method('findByUrl')
            ->with($domain->getId(), $url->getBase(), $datePeriod)
            ->willReturn([]);

        $this->visitRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Visit::class));

        $this->visitorRepository
            ->expects($this->once())
            ->method('update')
            ->with($existingVisitor);

        $this->visitorRepository->expects($this->once())
            ->method('exists')
            ->with($domain->getId(), $visitorHash)
            ->willReturn(true);

        $this->visitorRepository->expects($this->once())
            ->method('findByHash')
            ->with($domain->getId(), $visitorHash)
            ->willReturn($existingVisitor);

        $result = $this->trackingService->trackVisit($visit);

        $this->assertInstanceOf(Visit::class, $result);
        $this->assertTrue($existingVisitor->getTotalVisits() > 1);
    }

    public function testTrackVisitDetectsNewPage()
    {
        $domain = $this->createMockDomain();
        $url = new Url('https://example.com/new-page');
        $userAgent = new UserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $ipAddress = new IpAddress('192.168.1.1');
        $visitTime = new DateTime();
        $visitorHash = VisitorHash::fromIpAndUserAgent($ipAddress, $userAgent);
        $visitor = Visitor::createNew(
            $domain->getId(),
            $visitorHash,
            browser: Browser::CHROME,
            os: OperatingSystem::WINDOWS,
            device: Device::DESKTOP
        );

        $this->visitorRepository
            ->expects($this->once())
            ->method('findByHash')
            ->with($domain->getId(), $visitorHash)
            ->willReturn($visitor);

        $this->visitorRepository->expects($this->once())->method('update');
        $this->visitRepository->expects($this->once())->method('save');

        $visit = Visit::create(
            domainId: $domain->getId(),
            url: $url,
            pageTitle: 'Test Page',
            visitorIp: $ipAddress,
            userAgent: $userAgent,
            referrer: 'https://google.com',
        );
        $result = $this->trackingService->trackVisit($visit);

        $this->assertInstanceOf(Visit::class, $result);
    }

    private function createMockDomain(): Domain
    {
        return new Domain(
            id: 1,
            userId: 1,
            domain: 'example.com',
            apiKey: new ApiKey('test-api-key'),
        );
    }
}