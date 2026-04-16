<?php

namespace App\User\Infrastructure\Services;

use App\User\Domain\Interfaces\UserTokenGeneratorInterface;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use RuntimeException;

final class SanctumUserTokenGenerator implements UserTokenGeneratorInterface
{
    public function __construct(
        private EloquentUser $model,
    ) {}

    public function generate(string $userId, string $tokenName): string
    {
        $user = $this->model->newQuery()->where('uuid', $userId)->first();

        if ($user === null) {
            throw new RuntimeException('User not found for token generation.');
        }

        return $user->createToken($tokenName)->plainTextToken;
    }
}
