<?php

namespace App\SaleLine\Infrastructure\Persistence\Repositories;

use App\SaleLine\Domain\Entity\SaleLine;
use App\SaleLine\Domain\Interfaces\SaleLineRepositoryInterface;
use App\SaleLine\Infrastructure\Persistence\Models\EloquentSaleLine;
use Illuminate\Support\Facades\DB;

final class EloquentSaleLineRepository implements SaleLineRepositoryInterface
{
    public function __construct(
        private EloquentSaleLine $model,
    ) {}

    public function save(SaleLine $saleLine): void
    {
        $saleId = DB::table('sales')
            ->where('uuid', $saleLine->saleId()->value())
            ->value('id');

        $orderLineId = DB::table('order_lines')
            ->where('uuid', $saleLine->orderLineId()->value())
            ->value('id');

        $this->model->newQuery()->create([
            'uuid' => $saleLine->id()->value(),
            'restaurant_id' => $saleLine->restaurantId()->value(),
            'sale_id' => $saleId,
            'order_line_id' => $orderLineId,
            'user_id' => $saleLine->userId()->value(),
            'quantity' => $saleLine->quantity()->value(),
            'price' => $saleLine->price()->value(),
            'tax_percentage' => $saleLine->taxPercentage()->value(),
            'created_at' => $saleLine->createdAt()->value(),
            'updated_at' => $saleLine->updatedAt()->value(),
        ]);
    }

    /**
     * @return array<int, SaleLine>
     */
    public function findBySaleId(string $saleId): array
    {
        $internalSaleId = DB::table('sales')
            ->where('uuid', $saleId)
            ->value('id');

        $models = $this->model->newQuery()
            ->where('sale_id', $internalSaleId)
            ->get();

        return $models->map(function (EloquentSaleLine $model) {
            $saleUuid = DB::table('sales')
                ->where('id', $model->sale_id)
                ->value('uuid');

            $orderLineUuid = DB::table('order_lines')
                ->where('id', $model->order_line_id)
                ->value('uuid');

            return SaleLine::fromPersistence(
                $model->uuid,
                $model->restaurant_id,
                $saleUuid,
                $orderLineUuid,
                $model->user_id,
                $model->quantity,
                $model->price,
                $model->tax_percentage,
                $model->created_at->toDateTimeImmutable(),
                $model->updated_at->toDateTimeImmutable(),
            );
        })->all();
    }
}