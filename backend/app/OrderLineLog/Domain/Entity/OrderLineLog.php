<?php

namespace App\OrderLineLog\Domain\Entity;

use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\OrderId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Uuid;

class OrderLineLog
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private OrderId $orderId,
        private ?string $orderLineId,
        private UserId $userId,
        private string $action,
        private ?int $oldQuantity,
        private ?int $newQuantity,
        private ?int $oldPrice,
        private ?int $newPrice,
        private ?string $reason,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        OrderId $orderId,
        ?string $orderLineId,
        UserId $userId,
        string $action,
        ?int $oldQuantity,
        ?int $newQuantity,
        ?int $oldPrice,
        ?int $newPrice,
        ?string $reason,
    ): self {
        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            $orderId,
            $orderLineId,
            $userId,
            $action,
            $oldQuantity,
            $newQuantity,
            $oldPrice,
            $newPrice,
            $reason,
            $now,
            $now,
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
    public function orderLineId(): ?string
    {
        return $this->orderLineId;
    }
    public function userId(): UserId
    {
        return $this->userId;
    }
    public function action(): string
    {
        return $this->action;
    }
    public function oldQuantity(): ?int
    {
        return $this->oldQuantity;
    }
    public function newQuantity(): ?int
    {
        return $this->newQuantity;
    }
    public function oldPrice(): ?int
    {
        return $this->oldPrice;
    }
    public function newPrice(): ?int
    {
        return $this->newPrice;
    }
    public function reason(): ?string
    {
        return $this->reason;
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
