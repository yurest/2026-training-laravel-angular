<?php

namespace App\OrderLineLog\Infrastructure\Persistence\Repositories;

use App\OrderLineLog\Domain\Entity\OrderLineLog;
use App\OrderLineLog\Domain\Interfaces\OrderLineLogRepositoryInterface;
use App\OrderLineLog\Infrastructure\Persistence\Models\EloquentOrderLineLog;

final class EloquentOrderLineLogRepository implements OrderLineLogRepositoryInterface
{
    public function __construct(
        private EloquentOrderLineLog $model,
    ) {}

    public function save(OrderLineLog $orderLineLog): void
    {
        $this->model->newQuery()->create([
            'uuid' => $orderLineLog->id()->value(),
            'restaurant_id' => $orderLineLog->restaurantId()->value(),
            'order_id' => $orderLineLog->orderId()->value(),
            'order_line_id' => $orderLineLog->orderLineId(),
            'user_id' => $orderLineLog->userId()->value(),
            'action' => $orderLineLog->action(),
            'old_quantity' => $orderLineLog->oldQuantity(),
            'new_quantity' => $orderLineLog->newQuantity(),
            'old_price' => $orderLineLog->oldPrice(),
            'new_price' => $orderLineLog->newPrice(),
            'reason' => $orderLineLog->reason(),
            'created_at' => $orderLineLog->createdAt()->value(),
            'updated_at' => $orderLineLog->updatedAt()->value(),
        ]);
    }
}