<?php

namespace App\Restaurant\Infrastructure\Persistence\Repositories;

use App\Restaurant\Domain\Entity\Restaurant;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;

final class EloquentRestaurantRepository implements RestaurantRepositoryInterface
{
    public function __construct(
        private EloquentRestaurant $model,
    ) {}

    public function save(Restaurant $restaurant): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $restaurant->id()->value()],
            [
                'name' => $restaurant->name()->value(),
                'legal_name' => $restaurant->legalName()->value(),
                'tax_id' => $restaurant->taxId()->value(),
                'email' => $restaurant->email()->value(),
                'password' => $restaurant->passwordHash()->value(),
                'created_at' => $restaurant->createdAt()->value(),
                'updated_at' => $restaurant->updatedAt()->value(),
            ]
        );
    }

    public function findById(string $id): ?Restaurant
    {
        $model = $this->model->newQuery()->where('uuid', $id)->first();

        if ($model === null) {
            return null;
        }

        return Restaurant::fromPersistence(
            $model->uuid,
            $model->name,
            $model->legal_name,
            $model->tax_id,
            $model->email,
            $model->password,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * @return array<int, Restaurant>
     */
    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models->map(function (EloquentRestaurant $model) {
            return Restaurant::fromPersistence(
                $model->uuid,
                $model->name,
                $model->legal_name,
                $model->tax_id,
                $model->email,
                $model->password,
                $model->created_at->toDateTimeImmutable(),
                $model->updated_at->toDateTimeImmutable(),
            );
        })->all();
    }

    public function delete(Restaurant $restaurant): void
    {
        $this->model->newQuery()->where('uuid', $restaurant->id()->value())->delete();
    }
}
