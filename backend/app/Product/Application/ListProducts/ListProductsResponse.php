<?php

namespace App\Product\Application\ListProducts;

final readonly class ListProductsResponse
{
    private function __construct(
        public array $items,
    ) {}

    public static function create(array $items): self
    {
        return new self(
            items: $items,
        );
    }

    public function toArray(): array
    {
        return [
            'items' => array_map(
                static fn (ListProductsItemResponse $item): array => $item->toArray(),
                $this->items,
            ),
        ];
    }
}
