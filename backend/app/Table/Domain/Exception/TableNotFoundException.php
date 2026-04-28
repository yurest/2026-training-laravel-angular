<?php

namespace App\Table\Domain\Exception;

final class TableNotFoundException extends \RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Table with id "%s" not found.', $id));
    }
}
