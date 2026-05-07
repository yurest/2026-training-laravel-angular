<?php

namespace App\Sale\Application\GetSales;

use App\Sale\Application\GetSale\GetSaleResponse;
use App\Sale\Domain\Interfaces\SaleRepositoryInterface;

final class GetSales
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        ?string $date = null,
        ?string $from = null,
        ?string $to = null,
        ?string $shift = null,
    ): GetSalesResponse {
        if ($date !== null && $shift !== null) {
            $sales = $this->saleRepository->findByRestaurantAndShift(
                $restaurantId,
                $date,
                $shift,
            );
        } elseif ($date !== null) {
            $sales = $this->saleRepository->findByRestaurantAndDate($restaurantId, $date);
        } elseif ($from !== null && $to !== null) {
            $sales = $this->saleRepository->findByRestaurantAndDateRange($restaurantId, $from, $to);
        } else {
            $sales = $this->saleRepository->findAllByRestaurant($restaurantId);
        }

        $response = array_map(
            fn ($sale) => GetSaleResponse::create($sale),
            $sales,
        );

        return GetSalesResponse::create($response);
    }
}