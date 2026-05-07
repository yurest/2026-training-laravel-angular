<?php

namespace App\Tables\Application\GetTable;

final readonly class GetTableCommand
{
    public function __construct(
        public string $id,
    ) {}
}
