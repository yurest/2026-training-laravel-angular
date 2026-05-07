<?php

namespace App\Tax\Domain\Exception;

final class TaxNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self("Tax with id {$id} not found.");
    }
}
