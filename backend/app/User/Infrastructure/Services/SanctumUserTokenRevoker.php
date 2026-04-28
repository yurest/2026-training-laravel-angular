<?php

namespace App\User\Infrastructure\Services;

use App\User\Domain\Interfaces\UserTokenRevokerInterface;
use Laravel\Sanctum\PersonalAccessToken;

final class SanctumUserTokenRevoker implements UserTokenRevokerInterface
{
    public function revoke(string $plainTextToken): void
    {
        $accessToken = PersonalAccessToken::findToken($plainTextToken);

        if ($accessToken === null) {
            return;
        }

        $accessToken->delete();
    }
}
