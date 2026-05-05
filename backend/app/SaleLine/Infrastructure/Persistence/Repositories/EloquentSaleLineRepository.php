<?php

namespace App\SaleLine\Infrastructure\Persistence\Repositories;

use App\SaleLine\Domain\Entity\SaleLine;
use App\SaleLine\Domain\Interfaces\SaleLineRepositoryInterface;
use App\SaleLine\Infrastructure\Persistence\Models\EloquentSaleLine;

final class EloquentSaleLineRepository implements SaleLineRepositoryInterface
{
    public function __construct(
        private EloquentSaleLine $model,
    ) {}

    public function save(SaleLine $saleLine): void
    {
        $this->model->newQuery()->create([
            'uuid' => $saleLine->id()->value(),
            'restaurant_id' => $saleLine->restaurantId()->value(),
            'sale_id' => $saleLine->saleId()->value(),
            'order_line_id' => $saleLine->orderLineId()->value(),
            'user_id' => $saleLine->userId()->value(),
            'quantity' => $saleLine->quantity()->value(),
            'price' => $saleLine->price()->value(),
            'tax_percentage' => $saleLine->taxPercentage()->value(),
            'created_at' => $saleLine->createdAt()->value(),
            'updated_at' => $saleLine->updatedAt()->value(),
        ]);
    }
}