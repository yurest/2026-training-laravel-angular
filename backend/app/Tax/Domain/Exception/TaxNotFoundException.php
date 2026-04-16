<?php

namespace App\Tax\Domain\Exception;

final class TaxNotFoundException extends \RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Tax with id %s not found.', $id));
    }
}