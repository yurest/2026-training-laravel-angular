<?php

namespace App\Tax\Application\GetTax;

final readonly class GetTaxCommand
{
    public function __construct(
        public string $id,
    ) {}
}
