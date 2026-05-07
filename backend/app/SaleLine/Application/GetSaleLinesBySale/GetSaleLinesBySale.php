<?php

namespace App\SaleLine\Application\GetSaleLinesBySale;

use App\SaleLine\Domain\Interfaces\SaleLineRepositoryInterface;

final class GetSaleLinesBySale
{
    public function __construct(
        private SaleLineRepositoryInterface $saleLineRepository,
    ) {}

    public function __invoke(string $saleId): GetSaleLinesBySaleResponse
    {
        $saleLines = $this->saleLineRepository->findBySaleId($saleId);

        $response = array_map(
            fn ($saleLine) => GetSaleLineResponse::create($saleLine),
            $saleLines,
        );

        return GetSaleLinesBySaleResponse::create($response);
    }
}