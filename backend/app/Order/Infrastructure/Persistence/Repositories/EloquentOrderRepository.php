<?php

namespace App\Order\Infrastructure\Persistence\Repositories;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;


final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EloquentOrder $model,
    ) {}

    public function save(Order $order): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $order->id()->value()],
            [
                'restaurant_id' => $order->restaurantId()->value(),
                'status' => $order->status()->value(),
                'table_id' => $order->tableId()->value(),
                'opened_by_user_id' => $order->openedByUserId()->value(),
                'closed_by_user_id' => $order->closedByUserId()?->value(),
                'diners' => $order->diners()->value(),
                'opened_at' => $order->openedAt()->value(),
                'closed_at' => $order->closedAt()?->value(),
                'created_at' => $order->createdAt()->value(),
                'updated_at' => $order->updatedAt()->value(),
            ]
        );
    }

    public function findById(string $id): ?Order
    {
        $model = $this->model->newQuery()->where('uuid', $id)->first();

        if ($model === null) {
            return null;
        }

        return Order::fromPersistence(
            $model->uuid,
            (string) $model->restaurant_id,
            $model->status,
            (string) $model->table_id,
            (string) $model->opened_by_user_id,
            $model->closed_by_user_id ? (string) $model->closed_by_user_id : null,
            $model->diners,
            $model->opened_at->toDateTimeImmutable(),
            $model->closed_at?->toDateTimeImmutable(),
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models->map(function (EloquentOrder $model) {
            return Order::fromPersistence(
                $model->uuid,
                (string) $model->restaurant_id,
                $model->status,
                (string) $model->table_id,
                (string) $model->opened_by_user_id,
                $model->closed_by_user_id ? (string) $model->closed_by_user_id : null,
                $model->diners,
                $model->opened_at->toDateTimeImmutable(),
                $model->closed_at?->toDateTimeImmutable(),
                $model->created_at->toDateTimeImmutable(),
                $model->updated_at->toDateTimeImmutable(),
            );
        })->all();
    }

    public function delete(Order $order): void
    {
        $this->model->newQuery()->where('uuid', $order->id()->value())->delete();
    }
}
