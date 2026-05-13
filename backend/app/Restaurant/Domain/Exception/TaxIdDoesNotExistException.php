<?php

namespace App\Restaurant\Domain\Exception;

final class TaxIdDoesNotExistException extends \DomainException
{
    public static function create(string $taxId): self
    {
        return new self("Tax ID {$taxId} does not exist yet. Use New Company to create the first restaurant.");
    }
}
