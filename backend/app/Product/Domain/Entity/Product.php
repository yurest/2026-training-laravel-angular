<?php

namespace App\Product\Domain\Entity;

use App\Product\Domain\ValueObject\ProductImageSrc;
use App\Product\Domain\ValueObject\ProductName;
use App\Product\Domain\ValueObject\ProductPrice;
use App\Product\Domain\ValueObject\ProductStock;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\FamilyId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\TaxId;
use App\Shared\Domain\ValueObject\Uuid;

class Product
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private FamilyId $familyId,
        private TaxId $taxId,
        private ProductStock $stock,
        private ProductImageSrc $imageSrc,
        private bool $active,
        private ProductName $name,
        private ProductPrice $price,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        FamilyId $familyId,
        TaxId $taxId,
        ProductStock $stock,
        ProductImageSrc $imageSrc,
        ProductName $name,
        ProductPrice $price,
    ): self {

        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            $familyId,
            $taxId,
            $stock,
            $imageSrc,
            true,
            $name,
            $price,
            $now,
            $now,
        );
    }

    public static function fromPersistence(
        string $id,
        string $restaurantId,
        string $familyId,
        string $taxId,
        int $stock,
        ?string $imageSrc,
        bool $active,
        string $name,
        int $price,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            Uuid::create($id),
            RestaurantId::create($restaurantId),
            FamilyId::create($familyId),
            TaxId::create($taxId),
            ProductStock::create($stock),
            ProductImageSrc::create($imageSrc),
            $active,
            ProductName::create($name),
            ProductPrice::create($price),
            DomainDateTime::create($createdAt),
            DomainDateTime::create($updatedAt),
        );
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function restaurantId(): RestaurantId
    {
        return $this->restaurantId;
    }

    public function familyId(): FamilyId
    {
        return $this->familyId;
    }

    public function taxId(): TaxId
    {
        return $this->taxId;
    }

    public function stock(): ProductStock
    {
        return $this->stock;
    }

    public function imageSrc(): ProductImageSrc
    {
        return $this->imageSrc;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function name(): ProductName
    {
        return $this->name;
    }

    public function price(): ProductPrice
    {
        return $this->price;
    }

    public function createdAt(): DomainDateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): DomainDateTime
    {
        return $this->updatedAt;
    }

    public function update(
        FamilyId $familyId,
        TaxId $taxId,
        ProductStock $stock,
        ProductImageSrc $imageSrc,
        ProductName $name,
        ProductPrice $price,
        bool $active,
    ): self {
        return new self(
            $this->id,
            $this->restaurantId,
            $familyId,
            $taxId,
            $stock,
            $imageSrc,
            $active,
            $name,
            $price,
            $this->createdAt,
            DomainDateTime::now(),
        );
    }
}
