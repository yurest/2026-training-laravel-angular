<?php

namespace App\Product\Application\GetProducts;

use App\Product\Application\GetProduct\GetProductResponse;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;

final class GetProducts
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(): GetProductsResponse
    {
        $products = $this->productRepository->findAll();

        $productResponses = array_map(
            fn ($product) => GetProductResponse::create($product),
            $products,
        );

        return GetProductsResponse::create($productResponses);
    }
}
