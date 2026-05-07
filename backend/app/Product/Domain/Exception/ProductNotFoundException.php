<?php

namespace App\Product\Domain\Exception;

use RuntimeException;

final class ProductNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self("Product with ID {$id} not found.");
    }
}
