<?php

namespace App\User\Application\CreateUser;

use App\Shared\Domain\Interfaces\PasswordHasherInterface;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\User\Domain\Entity\User;
use App\User\Domain\Interfaces\UserRepositoryInterface;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserImageSrc;
use App\User\Domain\ValueObject\UserName;
use App\User\Domain\ValueObject\UserPin;
use App\User\Domain\ValueObject\UserRole;

final class CreateUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $role,
        ?string $imageSrc,
        string $name,
        string $email,
        string $plainPassword,
        string $pin,
    ): CreateUserResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $roleVO = UserRole::create($role);
        $imageSrcVO = UserImageSrc::create($imageSrc);
        $nameVO = UserName::create($name);
        $emailVO = Email::create($email);
        $passwordHashVO = PasswordHash::create(
            $this->passwordHasher->hash($plainPassword),
        );
        $pinVO = UserPin::create($pin);

        $user = User::dddCreate(
            $restaurantIdVO,
            $roleVO,
            $imageSrcVO,
            $nameVO,
            $emailVO,
            $passwordHashVO,
            $pinVO,
        );

        $this->userRepository->save($user);

        return CreateUserResponse::create($user);
    }
}