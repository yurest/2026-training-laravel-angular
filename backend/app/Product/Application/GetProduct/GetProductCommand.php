<?php

namespace App\Product\Application\GetProduct;

final readonly class GetProductCommand
{
    public function __construct(
        public string $id,
    ) {}
}
