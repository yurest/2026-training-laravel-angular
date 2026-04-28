<?php

namespace App\Restaurant\Domain\Exception;

final class RestaurantNotFoundException extends \RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Restaurant with id "%s" not found.', $id));
    }
}
