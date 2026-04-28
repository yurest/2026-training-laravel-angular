<?php

namespace App\User\Application\DeleteUser;

use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Interfaces\UserRepositoryInterface;

final class DeleteUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(string $id): void
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw UserNotFoundException::withId($id);
        }

        $this->userRepository->delete($user);
    }
}
