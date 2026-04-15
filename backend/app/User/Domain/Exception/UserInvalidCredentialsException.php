<?php

namespace App\User\Domain\Exception;

final class UserInvalidCredentialsException extends \RuntimeException
{
    public static function create(): self
    {
        return new self('Invalid credentials.');
    }
}