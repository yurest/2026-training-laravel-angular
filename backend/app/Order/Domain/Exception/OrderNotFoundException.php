<?php

namespace App\Order\Domain\Exception;

use RuntimeException;

final class OrderNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Order with id "%s" not found.', $id));
    }
}