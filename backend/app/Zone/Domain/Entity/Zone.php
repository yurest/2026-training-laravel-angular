<?php

namespace App\Zone\Domain\Entity;

use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\Uuid;
use App\Zone\Domain\ValueObject\ZoneName;

class Zone
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private ZoneName $name,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        ZoneName $name,
    ): self {
        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            $name,
            $now,
            $now,
        );
    }

    public static function fromPersistence(
        string $id,
        string $restaurantId,
        string $name,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            Uuid::create($id),
            RestaurantId::create($restaurantId),
            ZoneName::create($name),
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

    public function name(): ZoneName
    {
        return $this->name;
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
        ZoneName $name,
    ): self {
        return new self(
            $this->id,
            $this->restaurantId,
            $name,
            $this->createdAt,
            DomainDateTime::now(),
        );
    }
}
