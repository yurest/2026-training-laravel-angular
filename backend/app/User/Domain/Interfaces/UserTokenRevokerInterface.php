<?php

namespace App\User\Domain\Interfaces;

interface UserTokenRevokerInterface
{
    public function revoke(string $plainTextToken): void;
}
