<?php

namespace App\User\Domain\Exception;

final class UserNotFoundException extends \RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('User with id "%s" not found.', $id));
    }
}
