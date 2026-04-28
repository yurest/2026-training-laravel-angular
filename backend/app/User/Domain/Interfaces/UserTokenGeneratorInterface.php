<?php

namespace App\User\Domain\Interfaces;

interface UserTokenGeneratorInterface
{
    public function generate(string $userId, string $tokenName): string;
}
