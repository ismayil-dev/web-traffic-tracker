<?php

declare(strict_types=1);

namespace TrafficTracker\Domain\Service;

use TrafficTracker\Domain\Entity\User;
use TrafficTracker\Domain\Exception\UserNotFound;
use TrafficTracker\Domain\Repositories\UserRepositoryInterface;

readonly class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @throws UserNotFound
     */
    public function retrieveUser(int $userId): User
    {
        $user = $this->userRepository->findById($userId);

        if (is_null($user)) {
            throw new UserNotFound();
        }

        return $user;
    }
}
