<?php

declare(strict_types=1);

namespace App\Product\Application\ListActiveProducts;

use App\Product\Application\ListProducts\ListProducts;
use App\Product\Application\ListProducts\ListProductsCommand;
use App\Product\Application\ListProducts\ListProductsResponse;

final class ListActiveProducts
{
    public function __construct(
        private ListProducts $listProducts,
    ) {}

    public function __invoke(ListActiveProductsCommand $command): ListProductsResponse
    {
        return ($this->listProducts)(
            new ListProductsCommand(
                includeDeleted: false,
                onlyActive: true,
            ),
        );
    }
}
