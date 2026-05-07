<?php

namespace App\Sale\Domain\Exception;

use RuntimeException;

final class SaleNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Sale with id "%s" not found.', $id));
    }
}