<?php

namespace App\User\Application\UpdateUser;

use App\Shared\Domain\ValueObject\Email;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Interfaces\UserRepositoryInterface;
use App\User\Domain\ValueObject\UserImageSrc;
use App\User\Domain\ValueObject\UserName;
use App\User\Domain\ValueObject\UserPin;
use App\User\Domain\ValueObject\UserRole;

final class UpdateUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(
        string $id,
        ?string $role = null,
        ?string $imageSrc = null,
        ?string $name = null,
        ?string $email = null,
        ?string $pin = null,
    ): UpdateUserResponse {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw UserNotFoundException::withId($id);
        }

        $roleVO = $role !== null
            ? UserRole::create($role)
            : $user->role();

        $imageSrcVO = $imageSrc !== null
            ? UserImageSrc::create($imageSrc)
            : $user->imageSrc();

        $nameVO = $name !== null
            ? UserName::create($name)
            : $user->name();

        $emailVO = $email !== null
            ? Email::create($email)
            : $user->email();

        $pinVO = $pin !== null
            ? UserPin::create($pin)
            : $user->pin();

        $user = $user->update(
            $roleVO,
            $imageSrcVO,
            $nameVO,
            $emailVO,
            $pinVO,
        );

        $this->userRepository->save($user);

        return UpdateUserResponse::create($user);
    }
}
