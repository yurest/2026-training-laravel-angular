<?php

namespace App\OrderLine\Infrastructure\Persistence\Repositories;

use App\OrderLine\Domain\Entity\OrderLine;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;
use App\OrderLine\Infrastructure\Persistence\Models\EloquentOrderLine;

final class EloquentOrderLineRepository implements OrderLineRepositoryInterface
{
    public function __construct(
        private EloquentOrderLine $model,
    ) {}

    public function save(OrderLine $orderLine): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $orderLine->id()->value()],
            [
                'restaurant_id' => $orderLine->restaurantId()->value(),
                'order_id' => $orderLine->orderId()->value(),
                'product_id' => $orderLine->productId()->value(),
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

        return OrderLine::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $model->order_id,
            $model->product_id,
            $model->user_id,
            $model->quantity,
            $model->price,
            $model->tax_percentage,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * @return array<int, OrderLine>
     */
    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models->map(function (EloquentOrderLine $model) {
            return OrderLine::fromPersistence(
                $model->uuid,
                $model->restaurant_id,
                $model->order_id,
                $model->product_id,
                $model->user_id,
                $model->quantity,
                $model->price,
                $model->tax_percentage,
                $model->created_at->toDateTimeImmutable(),
                $model->updated_at->toDateTimeImmutable(),
            );
        })->all();
    }

    public function delete(OrderLine $orderLine): void
    {
        $this->model->newQuery()->where('uuid', $orderLine->id()->value())->delete();
    }
}