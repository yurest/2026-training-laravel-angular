<?php

declare(strict_types=1);

namespace App\Product\Application\ListProducts;

use App\Product\Domain\Interfaces\ProductRepositoryInterface;

class ListProducts
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(ListProductsCommand $command): ListProductsResponse
    {
        $products = $this->productRepository->findAll($command->includeDeleted);

        if ($command->onlyActive) {
            $products = array_filter($products, fn ($p) => $p->isActive());
        }

        $items = array_values(array_map(
            static fn ($product): ListProductsItemResponse => ListProductsItemResponse::create(
                id: $product->id()->value(),
                familyId: $product->familyId()->value(),
                taxId: $product->taxId()->value(),
                imageSrc: $product->imageSrc()->value(),
                name: $product->name()->value(),
                price: $product->price()->value(),
                stock: $product->stock()->value(),
                active: $product->isActive(),
                allergens: $product->allergens()->values(),
                createdAt: $product->createdAt()->format(\DateTimeInterface::ATOM),
                updatedAt: $product->updatedAt()->format(\DateTimeInterface::ATOM),
            ),
            $products,
        ));

        return ListProductsResponse::create($items);
    }
}
