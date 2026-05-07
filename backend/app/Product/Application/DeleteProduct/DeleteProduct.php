<?php

namespace App\Product\Application\DeleteProduct;

use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;

class DeleteProduct
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(DeleteProductCommand $command): void
    {
        $deleted = $this->productRepository->deleteById($command->id);

        if (! $deleted) {
            throw ProductNotFoundException::withId($command->id);
        }
    }
}
