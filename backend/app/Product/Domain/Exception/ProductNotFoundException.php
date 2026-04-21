<?php

namespace App\Product\Domain\Exception;

final class ProductNotFoundException extends \RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Product with id "%s" not found.', $id));
    }
}
