<?php

namespace App\Tax\Application\ListTaxes;

final readonly class ListTaxesResponse
{
    /**
     * @param  array<int, array<string, int|string>>  $items
     */
    private function __construct(
        public array $items,
    ) {}

    /**
     * @param  array<int, array<string, int|string>>  $items
     */
    public static function create(array $items): self
    {
        return new self(
            items: $items,
        );
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
