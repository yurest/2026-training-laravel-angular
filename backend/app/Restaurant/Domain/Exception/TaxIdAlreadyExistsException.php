<?php

namespace App\Restaurant\Domain\Exception;

final class TaxIdAlreadyExistsException extends \DomainException
{
    public static function create(string $taxId): self
    {
        return new self("Tax ID {$taxId} already exists. Use the existing company action to add a branch.");
    }
}
