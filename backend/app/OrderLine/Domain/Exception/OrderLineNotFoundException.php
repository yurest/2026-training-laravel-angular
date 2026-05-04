<?php

namespace App\OrderLine\Domain\Exception;

use RuntimeException;

final class OrderLineNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(
            sprintf('Order line with id "%s" not found.', $id)
        );
    }
}