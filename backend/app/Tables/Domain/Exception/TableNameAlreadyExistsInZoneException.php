<?php

namespace App\Tables\Domain\Exception;

final class TableNameAlreadyExistsInZoneException extends \DomainException
{
    public static function withName(string $name): self
    {
        return new self("Table with name {$name} already exists in this zone.");
    }
}
