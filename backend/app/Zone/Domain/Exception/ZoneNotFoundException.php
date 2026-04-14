<?php

namespace App\Zone\Domain\Exception;

final class ZoneNotFoundException extends \RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Zone with id "%s" not found.', $id));
    }
}