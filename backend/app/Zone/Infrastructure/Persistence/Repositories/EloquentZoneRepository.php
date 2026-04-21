<?php

namespace App\Zone\Infrastructure\Persistence\Repositories;

use App\Zone\Domain\Entity\Zone;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;
use App\Zone\Infrastructure\Persistence\Models\EloquentZone;

final class EloquentZoneRepository implements ZoneRepositoryInterface
{
    public function __construct(
        private EloquentZone $model,
    ) {}

    public function save(Zone $zone): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $zone->id()->value()],
            [
                'restaurant_id' => $zone->restaurantId()->value(),
                'name' => $zone->name()->value(),
                'created_at' => $zone->createdAt()->value(),
                'updated_at' => $zone->updatedAt()->value(),
            ]
        );
    }

    public function findById(string $id): ?Zone
    {
        $model = $this->model->newQuery()->where('uuid', $id)->first();

        if ($model === null) {
            return null;
        }

        return Zone::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $model->name,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * @return array<int, Zone>
     */
    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models->map(function (EloquentZone $model) {
            return Zone::fromPersistence(
                $model->uuid,
                $model->restaurant_id,
                $model->name,
                $model->created_at->toDateTimeImmutable(),
                $model->updated_at->toDateTimeImmutable(),
            );
        })->all();
    }

    public function delete(Zone $zone): void
    {
        $this->model->newQuery()->where('uuid', $zone->id()->value())->delete();
    }
}
