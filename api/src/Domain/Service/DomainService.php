<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Service;

use Random\RandomException;
use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Entity\User;
use TrafficTracker\Domain\Exception\DomainException;
use TrafficTracker\Domain\Exception\DomainNotFound;
use TrafficTracker\Domain\Exception\InvalidApiKey;
use TrafficTracker\Domain\Repositories\DomainRepositoryInterface;
use TrafficTracker\Domain\ValueObject\ApiKey;

readonly class DomainService
{
    public function __construct(
        private DomainRepositoryInterface $domainRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws RandomException
     */
    public function registerDomain(User $user, string $domainName): array
    {
        if ($this->domainRepository->exists($domainName)) {
            throw new DomainException("Domain '{$domainName}' is already registered");
        }

        if (!$this->isValidDomainName($domainName)) {
            throw new DomainException('Invalid domain name format');
        }

        $apiKeyData = ApiKey::generate();
        $domain = Domain::create($user->getId(), $domainName, $apiKeyData['api_key']);

        $savedDomain = $this->domainRepository->save($domain);

        return [
            'api_key' => $apiKeyData['plain'],
            'domain' => $savedDomain,
        ];
    }

    /**
     * @throws InvalidApiKey
     * @throws DomainNotFound
     */
    public function authenticateDomain(string $apiKeyValue): Domain
    {
        if (empty($apiKeyValue) || strlen($apiKeyValue) < 64) {
            throw new InvalidApiKey();
        }

        $apiKey = ApiKey::fromPlainValue($apiKeyValue);
        $domain = $this->domainRepository->findByApiKey($apiKey);

        if (is_null($domain)) {
            throw new DomainNotFound();
        }

        return $domain;
    }

    /**
     * @throws DomainNotFound
     */
    public function findById(int $domainId): Domain
    {
        $domain = $this->domainRepository->findById($domainId);

        if (is_null($domain)) {
            throw new DomainNotFound();
        }

        return $domain;
    }

    public function getDomainUsageStats(Domain $domain): array
    {
        $domain = $this->domainRepository->findById($domain->getId());

        if (is_null($domain)) {
            throw new DomainException('Domain not found');
        }

        // This would typically aggregate data from other repositories
        // For now, returning basic domain info
        // TODO: Implement getDomainUsageStats() method.
        return [
            'domain_id' => $domain->getId(),
            'domain_name' => $domain->getDomain(),
            'created_at' => $domain->getCreatedAt()->format('Y-m-d H:i:s'),
            'api_key_masked' => $domain->getApiKey()->getMasked(),
        ];
    }

    private function isValidDomainName(string $domain): bool
    {
        // Basic domain validation
        if (empty($domain)) {
            return false;
        }

        // Check length
        if (strlen($domain) > 253) {
            return false;
        }

        // Check for valid characters and structure
        $pattern = '/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?$/';

        if (!preg_match($pattern, $domain)) {
            return false;
        }

        // Check for localhost and IP addresses (not allowed for tracking)
        // $domain === 'localhost' ||
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            return false;
        }

        return true;
    }
}
