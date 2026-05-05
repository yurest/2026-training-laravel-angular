<?php

namespace App\Sale\Domain\Interfaces;

use App\Sale\Domain\Entity\Sale;

interface SaleRepositoryInterface
{
    public function save(Sale $sale): void;

    public function findById(string $id): ?Sale;

    /**
     * @return array<int, Sale>
     */
    public function findAllByRestaurant(string $restaurantId): array;

    /**
     * @return array<int, Sale>
     */
    public function findByRestaurantAndDate(string $restaurantId, string $date): array;

    /**
     * @return array<int, Sale>
     */
    public function findByRestaurantAndDateRange(
        string $restaurantId,
        string $from,
        string $to,
    ): array;

    /**
     * @return array<int, Sale>
     */
    public function findByRestaurantAndShift(
        string $restaurantId,
        string $date,
        string $shift,
    ): array;
}
