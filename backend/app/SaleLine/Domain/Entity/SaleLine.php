<?php

namespace App\SaleLine\Domain\Entity;

use App\SaleLine\Domain\ValueObject\SaleLinePrice;
use App\SaleLine\Domain\ValueObject\SaleLineQuantity;
use App\SaleLine\Domain\ValueObject\SaleLineTaxPercentage;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\OrderLineId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\SaleId;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Uuid;

class SaleLine
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private SaleId $saleId,
        private OrderLineId $orderLineId,
        private UserId $userId,
        private SaleLineQuantity $quantity,
        private SaleLinePrice $price,
        private SaleLineTaxPercentage $taxPercentage,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        SaleId $saleId,
        OrderLineId $orderLineId,
        UserId $userId,
        SaleLineQuantity $quantity,
        SaleLinePrice $price,
        SaleLineTaxPercentage $taxPercentage,
    ): self {
        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            $saleId,
            $orderLineId,
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
        string $saleId,
        string $orderLineId,
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
            SaleId::create($saleId),
            OrderLineId::create($orderLineId),
            UserId::create($userId),
            SaleLineQuantity::create($quantity),
            SaleLinePrice::create($price),
            SaleLineTaxPercentage::create($taxPercentage),
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

    public function saleId(): SaleId
    {
        return $this->saleId;
    }

    public function orderLineId(): OrderLineId
    {
        return $this->orderLineId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function quantity(): SaleLineQuantity
    {
        return $this->quantity;
    }

    public function price(): SaleLinePrice
    {
        return $this->price;
    }

    public function taxPercentage(): SaleLineTaxPercentage
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
}