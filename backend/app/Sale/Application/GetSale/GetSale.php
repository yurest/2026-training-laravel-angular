<?php

namespace App\Sale\Application\GetSale;

use App\Sale\Domain\Exception\SaleNotFoundException;
use App\Sale\Domain\Interfaces\SaleRepositoryInterface;

final class GetSale
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
    ) {}

    public function __invoke(string $id): GetSaleResponse
    {
        $sale = $this->saleRepository->findById($id);

        if ($sale === null) {
            throw SaleNotFoundException::withId($id);
        }

        return GetSaleResponse::create($sale);
    }
}