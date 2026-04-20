<?php

namespace App\User\Application\LogoutUser;

use App\User\Domain\Interfaces\UserTokenRevokerInterface;

final class LogoutUser
{
    public function __construct(
        private UserTokenRevokerInterface $userTokenRevoker,
    ) {}

    public function __invoke(string $plainTextToken): void
    {
        $this->userTokenRevoker->revoke($plainTextToken);
    }
}
