<?php

namespace App\Sale\Infrastructure\Persistence\Repositories;

use App\Sale\Domain\Entity\Sale;
use App\Sale\Domain\Interfaces\SaleRepositoryInterface;
use App\Sale\Infrastructure\Persistence\Models\EloquentSale;
use Illuminate\Support\Facades\DB;

final class EloquentSaleRepository implements SaleRepositoryInterface
{
    public function __construct(
        private EloquentSale $model,
    ) {}

    public function save(Sale $sale): void
    {
        $orderId = DB::table('orders')
            ->where('uuid', $sale->orderId()->value())
            ->value('id');

        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $sale->id()->value()],
            [
                'restaurant_id' => $sale->restaurantId()->value(),
                'order_id' => $orderId,
                'user_id' => $sale->userId()->value(),
                'ticket_number' => $sale->ticketNumber()?->value(),
                'value_date' => $sale->valueDate()->value(),
                'total' => $sale->total()->value(),
                'created_at' => $sale->createdAt()->value(),
                'updated_at' => $sale->updatedAt()->value(),
            ]
        );
    }

    public function findById(string $id): ?Sale
    {
        $model = $this->model->newQuery()->where('uuid', $id)->first();

        if ($model === null) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    /**
     * @return array<int, Sale>
     */
    public function findAllByRestaurant(string $restaurantId): array
    {
        $models = $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->orderByDesc('value_date')
            ->get();

        return $models
            ->map(fn (EloquentSale $model) => $this->mapToEntity($model))
            ->all();
    }

    /**
     * @return array<int, Sale>
     */
    public function findByRestaurantAndDate(string $restaurantId, string $date): array
    {
        $models = $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->whereDate('value_date', $date)
            ->orderByDesc('value_date')
            ->get();

        return $models
            ->map(fn (EloquentSale $model) => $this->mapToEntity($model))
            ->all();
    }

    /**
     * @return array<int, Sale>
     */
    public function findByRestaurantAndDateRange(
        string $restaurantId,
        string $from,
        string $to,
    ): array {
        $models = $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->whereDate('value_date', '>=', $from)
            ->whereDate('value_date', '<=', $to)
            ->orderByDesc('value_date')
            ->get();

        return $models
            ->map(fn (EloquentSale $model) => $this->mapToEntity($model))
            ->all();
    }

    /**
     * @return array<int, Sale>
     */
    public function findByRestaurantAndShift(
        string $restaurantId,
        string $date,
        string $shift,
    ): array {
        $query = $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->whereDate('value_date', $date);

        if ($shift === 'comida') {
            $query
                ->whereTime('value_date', '>=', '12:00:00')
                ->whereTime('value_date', '<', '18:00:00');
        }

        if ($shift === 'cena') {
            $query
                ->whereTime('value_date', '>=', '18:00:00')
                ->whereTime('value_date', '<=', '23:59:59');
        }

        $models = $query
            ->orderByDesc('value_date')
            ->get();

        return $models
            ->map(fn (EloquentSale $model) => $this->mapToEntity($model))
            ->all();
    }

    private function mapToEntity(EloquentSale $model): Sale
    {
        $orderUuid = DB::table('orders')
            ->where('id', $model->order_id)
            ->value('uuid');

        return Sale::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $orderUuid,
            $model->user_id,
            $model->ticket_number,
            $model->value_date->toDateTimeImmutable(),
            $model->total,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }
}