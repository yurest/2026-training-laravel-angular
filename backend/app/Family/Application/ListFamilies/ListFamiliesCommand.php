<?php

namespace App\Family\Application\ListFamilies;

final readonly class ListFamiliesCommand
{
    public function __construct(
        public ?bool $includeDeleted,
        public ?bool $onlyActive,
    ) {}
}
