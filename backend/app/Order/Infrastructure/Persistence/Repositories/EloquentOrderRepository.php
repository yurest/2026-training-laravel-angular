<?php

namespace App\Order\Infrastructure\Persistence\Repositories;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use Illuminate\Support\Facades\DB;

final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EloquentOrder $model,
    ) {}

    public function save(Order $order): void
    {
        $tableId = DB::table('tables')
            ->where('uuid', $order->tableId()->value())
            ->value('id');

        $openedByUserId = DB::table('users')
            ->where('uuid', $order->openedByUserId()->value())
            ->value('id');

        $closedByUserId = null;

        if ($order->closedByUserId() !== null) {
            $closedByUserId = DB::table('users')
                ->where('uuid', $order->closedByUserId()->value())
                ->value('id');
        }

        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $order->id()->value()],
            [
                'restaurant_id' => $order->restaurantId()->value(),
                'status' => $order->status()->value(),
                'table_id' => $tableId,
                'opened_by_user_id' => $openedByUserId,
                'closed_by_user_id' => $closedByUserId,
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

        return $this->mapToEntity($model);
    }

    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models
            ->map(fn(EloquentOrder $model) => $this->mapToEntity($model))
            ->all();
    }

    /**
     * @return Order[]
     */
    public function findOpenByRestaurant(string $restaurantId): array
    {
        $models = $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'open')
            ->orderByDesc('opened_at')
            ->get();

        return $models
            ->map(fn(EloquentOrder $model) => $this->mapToEntity($model))
            ->all();
    }

    public function findOpenByTable(string $restaurantId, string $tableId): ?Order
    {
        $internalTableId = DB::table('tables')
            ->where('uuid', $tableId)
            ->value('id');

        $model = $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->where('table_id', $internalTableId)
            ->where('status', 'open')
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->mapToEntity($model);
    }




    public function delete(Order $order): void
    {
        $this->model->newQuery()
            ->where('uuid', $order->id()->value())
            ->delete();
    }

    private function mapToEntity(EloquentOrder $model): Order
    {
        $tableUuid = DB::table('tables')
            ->where('id', $model->table_id)
            ->value('uuid');

        $openedByUserUuid = DB::table('users')
            ->where('id', $model->opened_by_user_id)
            ->value('uuid');

        $closedByUserUuid = null;

        if ($model->closed_by_user_id !== null) {
            $closedByUserUuid = DB::table('users')
                ->where('id', $model->closed_by_user_id)
                ->value('uuid');
        }

        return Order::fromPersistence(
            $model->uuid,
            (string) $model->restaurant_id,
            $model->status,
            $tableUuid,
            $openedByUserUuid,
            $closedByUserUuid,
            $model->diners,
            $model->opened_at->toDateTimeImmutable(),
            $model->closed_at?->toDateTimeImmutable(),
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }
}
