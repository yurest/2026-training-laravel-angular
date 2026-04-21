<?php

namespace App\Tax\Application\GetTaxes;

use App\Tax\Application\GetTax\GetTaxResponse;

final readonly class GetTaxesResponse
{
    /**
     * @param  GetTaxResponse[]  $taxes
     */
    public function __construct(
        public array $taxes,
    ) {}

    /**
     * @param  GetTaxResponse[]  $taxes
     */
    public static function create(array $taxes): self
    {
        return new self($taxes);
    }

    /**
     * @return array<string, array<int, array<string, string|bool>>>
     */
    public function toArray(): array
    {
        return [
            'taxes' => array_map(
                fn (GetTaxResponse $tax) => $tax->toArray(),
                $this->taxes,
            ),
        ];
    }
}
