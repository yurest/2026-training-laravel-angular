<?php

namespace App\Family\Application\GetFamily;

final readonly class GetFamilyCommand
{
    public function __construct(
        public string $id,
    ) {}
}
