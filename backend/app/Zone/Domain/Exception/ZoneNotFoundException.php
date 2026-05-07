<?php

namespace App\Zone\Domain\Exception;

final class ZoneNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self("Zone with id {$id} not found.");
    }
}
