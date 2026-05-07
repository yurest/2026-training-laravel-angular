<?php

namespace App\Product\Application\ListProducts;

final readonly class ListProductsCommand
{
    public function __construct(
        public bool $includeDeleted,
        public bool $onlyActive,
    ) {}
}
