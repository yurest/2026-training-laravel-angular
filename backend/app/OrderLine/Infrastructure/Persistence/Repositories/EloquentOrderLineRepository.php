<?php

namespace App\OrderLine\Infrastructure\Persistence\Repositories;

use App\OrderLine\Domain\Entity\OrderLine;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;
use App\OrderLine\Infrastructure\Persistence\Models\EloquentOrderLine;
use Illuminate\Support\Facades\DB;

final class EloquentOrderLineRepository implements OrderLineRepositoryInterface
{
    public function __construct(
        private EloquentOrderLine $model,
    ) {}

    public function save(OrderLine $orderLine): void
    {
        $orderId = DB::table('orders')
            ->where('uuid', $orderLine->orderId()->value())
            ->value('id');

        $productId = DB::table('products')
            ->where('uuid', $orderLine->productId()->value())
            ->value('id');

        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $orderLine->id()->value()],
            [
                'restaurant_id' => $orderLine->restaurantId()->value(),
                'order_id' => $orderId,
                'product_id' => $productId,
                'user_id' => $orderLine->userId()->value(),
                'quantity' => $orderLine->quantity()->value(),
                'price' => $orderLine->price()->value(),
                'tax_percentage' => $orderLine->taxPercentage()->value(),
                'created_at' => $orderLine->createdAt()->value(),
                'updated_at' => $orderLine->updatedAt()->value(),
            ]
        );
    }

    public function findById(string $id): ?OrderLine
    {
        $model = $this->model->newQuery()->where('uuid', $id)->first();

        if ($model === null) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    /**
     * @return array<int, OrderLine>
     */
    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models
            ->map(fn (EloquentOrderLine $model) => $this->mapToEntity($model))
            ->all();
    }

    /**
     * @return array<int, OrderLine>
     */
    public function findByOrderId(string $orderId): array
    {
        $internalOrderId = DB::table('orders')
            ->where('uuid', $orderId)
            ->value('id');

        $models = $this->model->newQuery()
            ->where('order_id', $internalOrderId)
            ->get();

        return $models
            ->map(fn (EloquentOrderLine $model) => $this->mapToEntity($model))
            ->all();
    }

    public function delete(OrderLine $orderLine): void
    {
        $this->model->newQuery()
            ->where('uuid', $orderLine->id()->value())
            ->delete();
    }

    private function mapToEntity(EloquentOrderLine $model): OrderLine
    {
        $orderUuid = DB::table('orders')
            ->where('id', $model->order_id)
            ->value('uuid');

        $productUuid = DB::table('products')
            ->where('id', $model->product_id)
            ->value('uuid');

        return OrderLine::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $orderUuid,
            $productUuid,
            $model->user_id,
            $model->quantity,
            $model->price,
            $model->tax_percentage,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }
}