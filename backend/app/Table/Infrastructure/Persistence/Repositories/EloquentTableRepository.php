<?php

namespace App\Table\Infrastructure\Persistence\Repositories;

use App\Table\Domain\Entity\Table;
use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;

final class EloquentTableRepository implements TableRepositoryInterface
{
    public function __construct(
        private EloquentTable $model,
    ) {}

    public function save(Table $table): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $table->id()->value()],
            [
                'restaurant_id' => $table->restaurantId()->value(),
                'zone_id' => $table->zoneId()->value(),
                'name' => $table->name()->value(),
                'created_at' => $table->createdAt()->value(),
                'updated_at' => $table->updatedAt()->value(),
            ]
        );
    }

    public function findById(string $id): ?Table
    {
        $model = $this->model->newQuery()->where('uuid', $id)->first();

        if ($model === null) {
            return null;
        }

        return Table::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $model->zone_id,
            $model->name,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * @return array<int, Table>
     */
    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models->map(function (EloquentTable $model) {
            return Table::fromPersistence(
                $model->uuid,
                $model->restaurant_id,
                $model->zone_id,
                $model->name,
                $model->created_at->toDateTimeImmutable(),
                $model->updated_at->toDateTimeImmutable(),
            );
        })->all();
    }

    public function delete(Table $table): void
    {
        $this->model->newQuery()->where('uuid', $table->id()->value())->delete();
    }
}
