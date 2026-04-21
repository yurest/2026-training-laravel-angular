<?php

namespace App\Product\Application\GetProduct;

use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;

final class GetProduct
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(string $id): GetProductResponse
    {
        $product = $this->productRepository->findById($id);

        if ($product === null) {
            throw ProductNotFoundException::withId($id);
        }

        return GetProductResponse::create($product);
    }
}
