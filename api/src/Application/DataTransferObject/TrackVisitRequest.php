<?php

declare(strict_types=1);

namespace TrafficTracker\Application\DataTransferObject;

readonly class TrackVisitRequest
{
    public function __construct(
        public string $url,
        public string $ipAddress,
        public string $userAgent,
        public ?string $pageTitle = null,
        public ?string $referrer = null,
    ) {
    }
}
