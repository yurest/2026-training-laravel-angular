<?php

namespace App\User\Application\LoginUser;

use App\Shared\Domain\Interfaces\PasswordHasherInterface;
use App\Shared\Domain\ValueObject\Email;
use App\User\Domain\Exception\UserInvalidCredentialsException;
use App\User\Domain\Interfaces\UserRepositoryInterface;
use App\User\Domain\Interfaces\UserTokenGeneratorInterface;

final class LoginUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private UserTokenGeneratorInterface $userTokenGenerator,
    ) {}

    public function __invoke(
        string $email,
        string $plainPassword,
    ): LoginUserResponse {
        $emailVO = Email::create($email);

        $user = $this->userRepository->findByEmail($emailVO->value());

        if ($user === null) {
            throw UserInvalidCredentialsException::create();
        }

        if (!$this->passwordHasher->verify(
            $plainPassword,
            $user->passwordHash()->value(),
        )) {
            throw UserInvalidCredentialsException::create();
        }

        $token = $this->userTokenGenerator->generate(
            $user->id()->value(),
            'frontend',
        );

        return LoginUserResponse::create($user, $token);
    }
}
