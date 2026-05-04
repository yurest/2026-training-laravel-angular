<?php

namespace App\OrderLine\Domain\Entity;

use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\OrderId;
use App\Shared\Domain\ValueObject\ProductId;
use App\Shared\Domain\ValueObject\UserId;
use App\OrderLine\Domain\ValueObject\OrderLineQuantity;
use App\OrderLine\Domain\ValueObject\OrderLinePrice;
use App\OrderLine\Domain\ValueObject\OrderLineTaxPercentage;

class OrderLine
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private OrderId $orderId,
        private ProductId $productId,
        private UserId $userId,
        private OrderLineQuantity $quantity,
        private OrderLinePrice $price,
        private OrderLineTaxPercentage $taxPercentage,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        OrderId $orderId,
        ProductId $productId,
        UserId $userId,
        OrderLineQuantity $quantity,
        OrderLinePrice $price,
        OrderLineTaxPercentage $taxPercentage,
    ): self {
        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            $orderId,
            $productId,
            $userId,
            $quantity,
            $price,
            $taxPercentage,
            $now,
            $now,
        );
    }

    public static function fromPersistence(
        string $id,
        string $restaurantId,
        string $orderId,
        string $productId,
        string $userId,
        int $quantity,
        int $price,
        int $taxPercentage,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            Uuid::create($id),
            RestaurantId::create($restaurantId),
            OrderId::create($orderId),
            ProductId::create($productId),
            UserId::create($userId),
            OrderLineQuantity::create($quantity),
            OrderLinePrice::create($price),
            OrderLineTaxPercentage::create($taxPercentage),
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

    public function orderId(): OrderId
    {
        return $this->orderId;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function quantity(): OrderLineQuantity
    {
        return $this->quantity;
    }

    public function price(): OrderLinePrice
    {
        return $this->price;
    }

    public function taxPercentage(): OrderLineTaxPercentage
    {
        return $this->taxPercentage;
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
        OrderLineQuantity $quantity,
        OrderLinePrice $price,
        OrderLineTaxPercentage $taxPercentage,
    ): self {
        return new self(
            $this->id,
            $this->restaurantId,
            $this->orderId,
            $this->productId,
            $this->userId,
            $quantity,
            $price,
            $taxPercentage,
            $this->createdAt,
            DomainDateTime::now(),
        );
    }
}