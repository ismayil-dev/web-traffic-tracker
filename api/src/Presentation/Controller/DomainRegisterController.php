<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use Random\RandomException;
use TrafficTracker\Application\DataTransferObject\RegisterDomainRequest;
use TrafficTracker\Application\Handler\RegisterDomain;
use TrafficTracker\Domain\Exception\DomainException;
use TrafficTracker\Domain\Service\DomainService;
use TrafficTracker\Infrastructure\Http\RequestContext;
use TrafficTracker\Infrastructure\Http\Response;
use TrafficTracker\Infrastructure\Repository\DomainRepository;
use TrafficTracker\Presentation\Trait\HasJsonBody;

class DomainRegisterController
{
    use HasJsonBody;

    private RegisterDomain $domainRegistrar;

    public function __construct()
    {
        $this->domainRegistrar = new RegisterDomain(
            domainService: new DomainService(
                domainRepository: new DomainRepository()
            )
        );
    }

    /**
     * @throws DomainException
     * @throws RandomException
     */
    public function __invoke(): Response
    {
        $data = $this->getJsonBody();

        $validationError = $this->validateHttpRequest($data);

        if (!empty($validationError)) {
            return Response::unProcessableContent($validationError);
        }

        $request = new RegisterDomainRequest(RequestContext::getUser(), $data['domain']);
        $publicDomain = $this->domainRegistrar->execute($request);

        return Response::success($publicDomain->toArray());
    }

    private function validateHttpRequest(array $data): array
    {
        $errors = [];

        if (empty($data['domain'])) {
            $errors[] = 'Domain is required';
        }

        return $errors;
    }
}
