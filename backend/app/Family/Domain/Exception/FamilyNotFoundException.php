<?php

namespace App\Family\Domain\Exception;

final class FamilyNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self("Family with id {$id} not found.");
    }
}
