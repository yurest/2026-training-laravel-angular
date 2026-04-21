<?php

namespace App\User\Application\GetUsers;

use App\User\Application\GetUser\GetUserResponse;
use App\User\Domain\Interfaces\UserRepositoryInterface;

final class GetUsers
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(): GetUsersResponse
    {
        $users = $this->userRepository->findAll();

        $userResponses = array_map(
            fn ($user) => GetUserResponse::create($user),
            $users,
        );

        return GetUsersResponse::create($userResponses);
    }
}
