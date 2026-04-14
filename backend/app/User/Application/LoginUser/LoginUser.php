<?php

namespace App\User\Application\LoginUser;

use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\Interfaces\PasswordHasherInterface;
use App\User\Domain\Interfaces\UserRepositoryInterface;

class LoginUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(string $email, string $plainPassword): LoginUserResponse
    {
        $emailVO = Email::create($email);

        $user = $this->userRepository->findByEmail($emailVO->value());

        if ($user === null) {
            throw new \Exception('Invalid credentials');
        }

        if (!$this->passwordHasher->verify(
            $plainPassword,
            $user->passwordHash(),
        )) {
            throw new \Exception('Invalid credentials');
        }

        return LoginUserResponse::create($user);
    }
}