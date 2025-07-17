<?php

declare(strict_types=1);


use TrafficTracker\Presentation\Controller\AnalyticsController;
use TrafficTracker\Presentation\Controller\TopPagesController;
use TrafficTracker\Presentation\Controller\VisitorBreakdownController;
use TrafficTracker\Presentation\Controller\RecentVisitsController;
use TrafficTracker\Presentation\Controller\HistoricalController;
use TrafficTracker\Presentation\Controller\DomainRegisterController;
use TrafficTracker\Presentation\Controller\TrackVisitController;
use TrafficTracker\Presentation\Middleware\DomainApiKey;
use TrafficTracker\Presentation\Middleware\UserToken;
use TrafficTracker\Presentation\Middleware\UserTokenWithDomain;

return [
    '/api/v1/analytics' => [
        'method' => 'GET',
        'handler' => AnalyticsController::class,
        'middleware' => [
            UserTokenWithDomain::class,
        ],
    ],
    '/api/v1/analytics/top-pages' => [
        'method' => 'GET',
        'handler' => TopPagesController::class,
        'middleware' => [
            UserTokenWithDomain::class,
        ],
    ],
    '/api/v1/analytics/visitor-breakdown' => [
        'method' => 'GET',
        'handler' => VisitorBreakdownController::class,
        'middleware' => [
            UserTokenWithDomain::class,
        ],
    ],
    '/api/v1/analytics/recent-visits' => [
        'method' => 'GET',
        'handler' => RecentVisitsController::class,
        'middleware' => [
            UserTokenWithDomain::class,
        ],
    ],
    '/api/v1/analytics/historical' => [
        'method' => 'GET',
        'handler' => HistoricalController::class,
        'middleware' => [
            UserTokenWithDomain::class,
        ],
    ],
    '/api/v1/track-visit' => [
        'method' => 'POST',
        'handler' => TrackVisitController::class,
        'middleware' => [
            DomainApiKey::class,
        ],
    ],
    '/api/v1/domain/register' => [
        'method' => 'POST',
        'middleware' => [
            UserToken::class,
        ],
        'handler' => DomainRegisterController::class,
    ],
];
