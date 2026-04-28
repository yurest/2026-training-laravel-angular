<?php

namespace App\User\Domain\Entity;

use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\Uuid;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserImageSrc;
use App\User\Domain\ValueObject\UserName;
use App\User\Domain\ValueObject\UserPin;
use App\User\Domain\ValueObject\UserRole;

class User
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private UserRole $role,
        private UserImageSrc $imageSrc,
        private UserName $name,
        private Email $email,
        private PasswordHash $passwordHash,
        private UserPin $pin,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        UserRole $role,
        UserImageSrc $imageSrc,
        UserName $name,
        Email $email,
        PasswordHash $passwordHash,
        UserPin $pin,
    ): self {
        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            $role,
            $imageSrc,
            $name,
            $email,
            $passwordHash,
            $pin,
            $now,
            $now,
        );
    }

    public static function fromPersistence(
        string $id,
        string $restaurantId,
        string $role,
        ?string $imageSrc,
        string $name,
        string $email,
        string $passwordHash,
        string $pin,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            Uuid::create($id),
            RestaurantId::create($restaurantId),
            UserRole::create($role),
            UserImageSrc::create($imageSrc),
            UserName::create($name),
            Email::create($email),
            PasswordHash::create($passwordHash),
            UserPin::create($pin),
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

    public function role(): UserRole
    {
        return $this->role;
    }

    public function imageSrc(): UserImageSrc
    {
        return $this->imageSrc;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function passwordHash(): PasswordHash
    {
        return $this->passwordHash;
    }

    public function pin(): UserPin
    {
        return $this->pin;
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
        UserRole $role,
        UserImageSrc $imageSrc,
        UserName $name,
        Email $email,
        UserPin $pin,
    ): self {
        return new self(
            $this->id,
            $this->restaurantId,
            $role,
            $imageSrc,
            $name,
            $email,
            $this->passwordHash,
            $pin,
            $this->createdAt,
            DomainDateTime::now(),
        );
    }

    public function changePassword(
        PasswordHash $passwordHash,
    ): self {
        return new self(
            $this->id,
            $this->restaurantId,
            $this->role,
            $this->imageSrc,
            $this->name,
            $this->email,
            $passwordHash,
            $this->pin,
            $this->createdAt,
            DomainDateTime::now(),
        );
    }
}
