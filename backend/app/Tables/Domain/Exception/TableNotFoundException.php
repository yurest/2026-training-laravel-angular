<?php

namespace App\Tables\Domain\Exception;

final class TableNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self("Table with id {$id} not found.");
    }
}
