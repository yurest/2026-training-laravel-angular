<?php

namespace App\Family\Application\SetFamilyActive;

final readonly class SetFamilyActiveCommand
{
    public function __construct(
        public string $id,
        public bool $active,
    ) {}
}
