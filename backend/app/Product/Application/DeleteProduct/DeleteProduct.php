<?php

namespace App\Product\Application\DeleteProduct;

use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;

final class DeleteProduct
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(string $id): void
    {
        $product = $this->productRepository->findById($id);

        if ($product === null) {
            throw ProductNotFoundException::withId($id);
        }

        $this->productRepository->delete($product);
    }
}
