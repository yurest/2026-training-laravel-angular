<?php

namespace App\Order\Domain\Entity;

use App\Order\Domain\ValueObject\Diners;
use App\Order\Domain\ValueObject\OrderStatus;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\Uuid;

class Order
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private OrderStatus $status,
        private string $tableId,
        private string $openedByUserId,
        private ?string $closedByUserId,
        private Diners $diners,
        private DomainDateTime $openedAt,
        private ?DomainDateTime $closedAt,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        string $tableId,
        string $openedByUserId,
        Diners $diners,
    ): self {
        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            OrderStatus::open(),
            $tableId,
            $openedByUserId,
            null,
            $diners,
            $now,
            null,
            $now,
            $now,
        );
    }

    public static function fromPersistence(
        string $id,
        string $restaurantId,
        string $status,
        string $tableId,
        string $openedByUserId,
        ?string $closedByUserId,
        int $diners,
        \DateTimeImmutable $openedAt,
        ?\DateTimeImmutable $closedAt,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            Uuid::create($id),
            RestaurantId::create($restaurantId),
            OrderStatus::create($status),
            $tableId,
            $openedByUserId,
            $closedByUserId,
            Diners::create($diners),
            DomainDateTime::create($openedAt),
            $closedAt ? DomainDateTime::create($closedAt) : null,
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

    public function status(): OrderStatus
    {
        return $this->status;
    }

    public function tableId(): string
    {
        return $this->tableId;
    }

    public function openedByUserId(): string
    {
        return $this->openedByUserId;
    }

    public function closedByUserId(): ?string
    {
        return $this->closedByUserId;
    }

    public function diners(): Diners
    {
        return $this->diners;
    }

    public function openedAt(): DomainDateTime
    {
        return $this->openedAt;
    }

    public function closedAt(): ?DomainDateTime
    {
        return $this->closedAt;
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