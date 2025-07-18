<?php

namespace TrafficTracker\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TrafficTracker\Domain\ValueObject\UserAgent;
use TrafficTracker\Domain\Enum\Browser;
use TrafficTracker\Domain\Enum\OperatingSystem;
use TrafficTracker\Domain\Enum\Device;
use InvalidArgumentException;

class UserAgentTest extends TestCase
{
    public function testParsesChromeUserAgent()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        );

        $this->assertEquals(Browser::CHROME, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::WINDOWS, $userAgent->getOS());
        $this->assertEquals(Device::DESKTOP, $userAgent->getDevice());
    }

    public function testParsesFirefoxUserAgent()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0'
        );

        $this->assertEquals(Browser::FIREFOX, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::WINDOWS, $userAgent->getOS());
        $this->assertEquals(Device::DESKTOP, $userAgent->getDevice());
    }

    public function testParsesSafariUserAgent()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15'
        );

        $this->assertEquals(Browser::SAFARI, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::MACOS, $userAgent->getOS());
        $this->assertEquals(Device::DESKTOP, $userAgent->getDevice());
    }

    public function testParsesEdgeUserAgent()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59'
        );

        $this->assertEquals(Browser::EDGE, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::WINDOWS, $userAgent->getOS());
        $this->assertEquals(Device::DESKTOP, $userAgent->getDevice());
    }

    public function testParsesMobileUserAgent()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1'
        );

        $this->assertEquals(Browser::SAFARI, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::IOS, $userAgent->getOS());
        $this->assertEquals(Device::IPHONE, $userAgent->getDevice());
    }

    public function testParsesTabletUserAgent()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1'
        );

        $this->assertEquals(Browser::SAFARI, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::IOS, $userAgent->getOS());
        $this->assertEquals(Device::IPAD, $userAgent->getDevice());
    }

    public function testParsesAndroidUserAgent()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Mobile Safari/537.36'
        );

        $this->assertEquals(Browser::CHROME, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::LINUX, $userAgent->getOS());
        $this->assertEquals(Device::ANDROID_PHONE, $userAgent->getDevice());
    }

    public function testHandlesLinuxUserAgent()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        );

        $this->assertEquals(Browser::CHROME, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::LINUX, $userAgent->getOS());
        $this->assertEquals(Device::DESKTOP, $userAgent->getDevice());
    }

    public function testHandlesUnknownBrowser()
    {
        $userAgent = new UserAgent(
            'SomeUnknownBrowser/1.0'
        );

        $this->assertEquals(Browser::UNKNOWN, $userAgent->getBrowser());
        $this->assertEquals(OperatingSystem::UNKNOWN, $userAgent->getOS());
        $this->assertEquals(Device::DESKTOP, $userAgent->getDevice());
    }

    public function testThrowsExceptionOnEmptyUserAgent()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User agent cannot be empty');
        
        new UserAgent('');
    }

    public function testToStringReturnsOriginalUserAgent()
    {
        $originalUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $userAgent = new UserAgent($originalUserAgent);

        $this->assertEquals($originalUserAgent, $userAgent->getValue());
    }

    public function testGetBrowserLabel()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        );

        $this->assertEquals('Chrome', $userAgent->getBrowser()->getLabel());
    }

    public function testGetOperatingSystemLabel()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        );

        $this->assertEquals('Windows', $userAgent->getOS()->getLabel());
    }

    public function testGetDeviceLabel()
    {
        $userAgent = new UserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        );

        $this->assertEquals('Desktop', $userAgent->getDevice()->getLabel());
    }
}