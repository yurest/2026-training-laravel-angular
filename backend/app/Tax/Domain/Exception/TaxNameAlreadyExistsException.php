<?php

namespace App\Tax\Domain\Exception;

final class TaxNameAlreadyExistsException extends \DomainException
{
    public static function withName(string $name): self
    {
        return new self("Tax with name {$name} already exists.");
    }
}
