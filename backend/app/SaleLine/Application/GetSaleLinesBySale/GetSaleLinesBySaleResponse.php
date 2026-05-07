<?php

namespace App\SaleLine\Application\GetSaleLinesBySale;

final readonly class GetSaleLinesBySaleResponse
{
    /**
     * @param array<int, GetSaleLineResponse> $saleLines
     */
    public function __construct(
        public array $saleLines,
    ) {}

    /**
     * @param array<int, GetSaleLineResponse> $saleLines
     */
    public static function create(array $saleLines): self
    {
        return new self($saleLines);
    }

    /**
     * @return array<string, array<int, array<string, string|int>>>
     */
    public function toArray(): array
    {
        return [
            'sale_lines' => array_map(
                fn (GetSaleLineResponse $saleLine) => $saleLine->toArray(),
                $this->saleLines,
            ),
        ];
    }
}