<?php

namespace App\Product\Application\GetProducts;

use App\Product\Application\GetProduct\GetProductResponse;

final readonly class GetProductsResponse
{
    /**
     * @param  GetProductResponse[]  $products
     */
    public function __construct(
        public array $products,
    ) {}

    /**
     * @param  GetProductResponse[]  $products
     */
    public static function create(array $products): self
    {
        return new self($products);
    }

    /**
     * @return array<string, array<int, array<string, string|int|bool|null>>>
     */
    public function toArray(): array
    {
        return [
            'products' => array_map(
                fn (GetProductResponse $product) => $product->toArray(),
                $this->products,
            ),
        ];
    }
}
