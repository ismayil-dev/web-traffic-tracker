<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

use TrafficTracker\Domain\Entity\Visit;

readonly class RecentVisits
{
    /**
     * @param array<Visit> $visits
     */
    public function __construct(
        public array $visits,
    ) {
    }

    public function toArray(): array
    {
        return array_map(function (Visit $visit) {
            return [
                'id' => $visit->getId(),
                'url' => $visit->getUrl()->getValue(),
                'page_title' => $visit->getPageTitle(),
                'visitor_ip' => $visit->getVisitorIp()->getValue(),
                'referrer' => $visit->getReferrer(),
                'user_agent' => $visit->getUserAgent()->getValue(),
                'browser' => $visit->getBrowser()->value,
                'os' => $visit->getOS()->value,
                'device' => $visit->getDevice()->value,
                'timestamp' => $visit->getTimestamp()->format('Y-m-d H:i:s'),
            ];
        }, $this->visits);
    }
}
