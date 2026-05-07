<?php

namespace App\Tax\Application\ListTaxes;

final readonly class ListTaxesCommand
{
    public function __construct(
        public ?bool $includeDeleted,
    ) {}
}
