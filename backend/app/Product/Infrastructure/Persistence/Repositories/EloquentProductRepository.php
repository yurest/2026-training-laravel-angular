<?php

namespace App\Product\Infrastructure\Persistence\Repositories;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\Models\EloquentProduct;
use Illuminate\Support\Facades\DB;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private EloquentProduct $model,
    ) {}

    public function save(Product $product): void
    {
        $familyId = $this->resolveInternalId('families', $product->familyId()->value());
        $taxId = $this->resolveInternalId('taxes', $product->taxId()->value());

        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $product->id()->value()],
            [
                'restaurant_id' => $product->restaurantId()->value(),
                'family_id' => $familyId,
                'tax_id' => $taxId,
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

        return $this->mapToEntity($model);
    }

    /**
     * @return array<int, Product>
     */
    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models
            ->map(fn (EloquentProduct $model) => $this->mapToEntity($model))
            ->all();
    }

    public function delete(Product $product): void
    {
        $this->model->newQuery()
            ->where('uuid', $product->id()->value())
            ->delete();
    }

    private function mapToEntity(EloquentProduct $model): Product
    {
        $familyUuid = DB::table('families')
            ->where('id', $model->family_id)
            ->value('uuid');

        $taxUuid = DB::table('taxes')
            ->where('id', $model->tax_id)
            ->value('uuid');

        return Product::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $familyUuid,
            $taxUuid,
            $model->stock,
            $model->image_src,
            $model->active,
            $model->name,
            $model->price,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    private function resolveInternalId(string $table, string $value): ?int
    {
        $id = DB::table($table)
            ->where('uuid', $value)
            ->value('id');

        if ($id !== null) {
            return (int) $id;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }
}