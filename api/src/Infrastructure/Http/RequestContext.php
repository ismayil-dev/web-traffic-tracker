<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Http;

use TrafficTracker\Domain\Entity\Domain;
use TrafficTracker\Domain\Entity\User;
use TrafficTracker\Infrastructure\Exception\DomainIsNotSet;
use TrafficTracker\Infrastructure\Exception\UserIsNotSet;

class RequestContext
{
    private static Domain $domain;

    private static User $user;

    public static function setDomain(Domain $domain): void
    {
        self::$domain = $domain;
    }

    /**
     * @throws DomainIsNotSet
     */
    public static function getDomain(): Domain
    {
        if (! isset(self::$domain)) {
            throw new DomainIsNotSet();
        }

        return self::$domain;
    }

    public static function setUser(User $user): void
    {
        self::$user = $user;
    }

    public static function getUser(): User
    {
        if (! isset(self::$user)) {
            throw new UserIsNotSet();
        }

        return self::$user;
    }
}
