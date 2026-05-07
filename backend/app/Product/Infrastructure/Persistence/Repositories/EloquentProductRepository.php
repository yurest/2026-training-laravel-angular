<?php

namespace App\Product\Infrastructure\Persistence\Repositories;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\Models\EloquentProduct;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private EloquentProduct $model,
    ) {}

    public function save(Product $product): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $product->id()->value()],
            [
                'restaurant_id' => $product->restaurantId()->value(),
                'family_id' => $product->familyId()->value(),
                'tax_id' => $product->taxId()->value(),
                'stock' => $product->stock()->value(),
                'image_src' => $product->imageSrc()->value(),
                'active' => $product->active(),
                'name' => $product->name()->value(),
                'price' => $product->price()->value(),
                'created_at' => $product->createdAt()->value(),
                'updated_at' => $product->updatedAt()->value(),
            ]
        );
    }

    public function findById(string $id): ?Product
    {
        $model = $this->model->newQuery()->where('uuid', $id)->first();

        if ($model === null) {
            return null;
        }

        return Product::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $model->family_id,
            $model->tax_id,
            $model->stock,
            $model->image_src,
            $model->active,
            $model->name,
            $model->price,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * @return array<int, Product>
     */
    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models->map(function (EloquentProduct $model) {
            return Product::fromPersistence(
                $model->uuid,
                $model->restaurant_id,
                $model->family_id,
                $model->tax_id,
                $model->stock,
                $model->image_src,
                $model->active,
                $model->name,
                $model->price,
                $model->created_at->toDateTimeImmutable(),
                $model->updated_at->toDateTimeImmutable(),
            );
        })->all();
    }

    public function delete(Product $product): void
    {
        $this->model->newQuery()->where('uuid', $product->id()->value())->delete();
    }
}