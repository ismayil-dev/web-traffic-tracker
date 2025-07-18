<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use TrafficTracker\Application\DataTransferObject\TrackVisitRequest;
use TrafficTracker\Application\Exception\UrlDoesNotMatchWithDomain;
use TrafficTracker\Application\Exception\VisitRejected;
use TrafficTracker\Application\Handler\TrackVisit;
use TrafficTracker\Domain\Service\TrackingService;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Repository\DailyStatsRepository;
use TrafficTracker\Infrastructure\Repository\VisitorRepository;
use TrafficTracker\Infrastructure\Repository\VisitRepository;
use TrafficTracker\Presentation\Trait\HasJsonBody;
use DateMalformedStringException;

class TrackVisitController
{
    use HasJsonBody;

    private TrackVisit $trackVisitUseCase;

    public function __construct()
    {
        $this->trackVisitUseCase = new TrackVisit(
            trackingService: new TrackingService(
                visitRepository: new VisitRepository(),
                visitorRepository: new VisitorRepository(),
                dailyStatsRepository: new DailyStatsRepository()
            )
        );
    }

    /**
     * @throws VisitRejected
     * @throws UrlDoesNotMatchWithDomain
     * @throws DateMalformedStringException
     */
    public function __invoke(): Response
    {
        $data = $this->getJsonBody();

        $validationError = $this->validateHttpRequest($data);

        if (!empty($validationError)) {
            return Response::unProcessableContent($validationError);
        }

        $request = new TrackVisitRequest(
            url: $data['url'],
            ipAddress: $this->getClientIpAddress(),
            userAgent: $data['user_agent'],
            pageTitle: $data['page_title'] ?? null,
            referrer: $data['referrer'] ?? null
        );

        $result = $this->trackVisitUseCase->execute($request, RequestContext::getDomain());

        return Response::created($result->toArray());
    }

    public function validateHttpRequest(array $data): array
    {
        $errors = [];

        if (empty($data['url'])) {
            $errors[] = 'URL is required';
        } elseif (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid URL format';
        }

        if (empty($data['user_agent'])) {
            $errors[] = 'User agent is required';
        }

        return $errors;
    }

    private function getClientIpAddress(): string
    {
        $headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
