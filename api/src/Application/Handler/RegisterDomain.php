<?php

declare(strict_types=1);

namespace TrafficTracker\Application\Handler;

use Random\RandomException;
use TrafficTracker\Application\DataTransferObject\PublicDomain;
use TrafficTracker\Application\DataTransferObject\RegisterDomainRequest;
use TrafficTracker\Domain\Exception\DomainException;
use TrafficTracker\Domain\Service\DomainService;

readonly class RegisterDomain
{
    public function __construct(
        private DomainService $domainService,
    ) {
    }

    /**
     * @throws RandomException
     * @throws DomainException
     */
    public function execute(RegisterDomainRequest $request): PublicDomain
    {
        $domain = $this->domainService->registerDomain($request->user, $request->domain);

        return new PublicDomain($domain['domain'], $domain['api_key']);
    }
}
