<?php

namespace App\Tax\Application\GetTaxes;

use App\Tax\Application\GetTax\GetTaxResponse;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;

final class GetTaxes
{
    public function __construct(
        private TaxRepositoryInterface $taxRepository,
    ) {}

    public function __invoke(): GetTaxesResponse
    {
        $taxes = $this->taxRepository->findAll();

        $taxResponses = array_map(
            fn ($tax) => GetTaxResponse::create($tax),
            $taxes,
        );

        return GetTaxesResponse::create($taxResponses);
    }
}
