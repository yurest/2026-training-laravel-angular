<?php

namespace App\User\Application\GetUser;

use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Interfaces\UserRepositoryInterface;

final class GetUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(string $id): GetUserResponse
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw UserNotFoundException::withId($id);
        }

        return GetUserResponse::create($user);
    }
}
