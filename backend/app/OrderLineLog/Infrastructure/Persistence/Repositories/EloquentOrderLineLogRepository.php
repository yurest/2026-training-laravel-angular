<?php

namespace App\OrderLineLog\Infrastructure\Persistence\Repositories;

use App\OrderLineLog\Domain\Entity\OrderLineLog;
use App\OrderLineLog\Domain\Interfaces\OrderLineLogRepositoryInterface;
use App\OrderLineLog\Infrastructure\Persistence\Models\EloquentOrderLineLog;
use Illuminate\Support\Facades\DB;

final class EloquentOrderLineLogRepository implements OrderLineLogRepositoryInterface
{
    public function __construct(
        private EloquentOrderLineLog $model,
    ) {}

    public function save(OrderLineLog $orderLineLog): void
    {
        $orderId = DB::table('orders')
            ->where('uuid', $orderLineLog->orderId()->value())
            ->value('id');

        $orderLineId = null;

        if ($orderLineLog->orderLineId() !== null) {
            $orderLineId = DB::table('order_lines')
                ->where('uuid', $orderLineLog->orderLineId())
                ->value('id');
        }

        $this->model->newQuery()->create([
            'uuid' => $orderLineLog->id()->value(),
            'restaurant_id' => $orderLineLog->restaurantId()->value(),
            'order_id' => $orderId,
            'order_line_id' => $orderLineId,
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