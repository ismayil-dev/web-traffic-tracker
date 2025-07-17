<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\ValueObject;

use InvalidArgumentException;
use TrafficTracker\Domain\Enum\Browser;
use TrafficTracker\Domain\Enum\Device;
use TrafficTracker\Domain\Enum\OperatingSystem;

readonly class UserAgent
{
    private Browser $browser;
    private OperatingSystem $os;
    private Device $device;

    public function __construct(
        private string $value,
        ?Browser $browser = null,
        ?OperatingSystem $os = null,
        ?Device $device = null,
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('User agent cannot be empty');
        }

        if ($browser !== null && $os !== null && $device !== null) {
            $this->browser = $browser;
            $this->os = $os;
            $this->device = $device;
        } else {
            $parsed = $this->parseAgent();
            $this->browser = $parsed['browser'];
            $this->os = $parsed['os'];
            $this->device = $parsed['device'];
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getBrowser(): Browser
    {
        return $this->browser;
    }

    public function getOS(): OperatingSystem
    {
        return $this->os;
    }

    public function getDevice(): Device
    {
        return $this->device;
    }

    /**
     * @return array{browser: Browser, os: OperatingSystem, device: Device}
     */
    private function parseAgent(): array
    {
        $agent = $this->value;

        return [
            'browser' => $this->extractBrowser($agent),
            'os' => $this->extractOS($agent),
            'device' => $this->extractDevice($agent),
        ];
    }

    private function extractBrowser(string $agent): Browser
    {
        foreach (Browser::getRegexPattern() as $pattern => $name) {
            if (stripos($agent, $pattern) !== false) {
                return $name;
            }
        }

        return Browser::UNKNOWN;
    }

    private function extractOS(string $agent): OperatingSystem
    {
        foreach (OperatingSystem::getRegexPattern() as $pattern => $name) {
            if (preg_match($pattern, $agent)) {
                return $name;
            }
        }

        return OperatingSystem::UNKNOWN;
    }

    private function extractDevice(string $agent): Device
    {
        foreach (Device::getRegexPattern() as $pattern => $name) {
            if (preg_match($pattern, $agent)) {
                return $name;
            }
        }

        return Device::DESKTOP;
    }
}
