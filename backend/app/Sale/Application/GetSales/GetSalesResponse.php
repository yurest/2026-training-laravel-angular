<?php

namespace App\Sale\Application\GetSales;

use App\Sale\Application\GetSale\GetSaleResponse;

final readonly class GetSalesResponse
{
    /**
     * @param array<int, GetSaleResponse> $sales
     */
    public function __construct(
        public array $sales,
    ) {}

    /**
     * @param array<int, GetSaleResponse> $sales
     */
    public static function create(array $sales): self
    {
        return new self($sales);
    }

    /**
     * @return array<string, array<int, array<string, string|int|null>>>
     */
    public function toArray(): array
    {
        return [
            'sales' => array_map(
                fn (GetSaleResponse $sale) => $sale->toArray(),
                $this->sales,
            ),
        ];
    }
}