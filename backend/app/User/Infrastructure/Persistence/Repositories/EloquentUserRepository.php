<?php

namespace App\User\Infrastructure\Persistence\Repositories;

use App\User\Domain\Entity\User;
use App\User\Domain\Interfaces\UserRepositoryInterface;
use App\User\Infrastructure\Persistence\Models\EloquentUser;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EloquentUser $model,
    ) {}

    public function save(User $user): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $user->id()->value()],
            [
                'restaurant_id' => $user->restaurantId()->value(),
                'role' => $user->role()->value(),
                'image_src' => $user->imageSrc()->value(),
                'name' => $user->name()->value(),
                'email' => $user->email()->value(),
                'password' => $user->passwordHash()->value(),
                'pin' => $user->pin()->value(),
                'created_at' => $user->createdAt()->value(),
                'updated_at' => $user->updatedAt()->value(),
            ]
        );
    }

    public function findById(string $id): ?User
    {
        $model = $this->model->newQuery()->where('uuid', $id)->first();

        if ($model === null) {
            return null;
        }

        return User::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $model->role,
            $model->image_src,
            $model->name,
            $model->email,
            $model->password,
            $model->pin,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    public function findByEmail(string $email): ?User
    {
        $model = $this->model->newQuery()->where('email', $email)->first();

        if ($model === null) {
            return null;
        }

        return User::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $model->role,
            $model->image_src,
            $model->name,
            $model->email,
            $model->password,
            $model->pin,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * @return array<int, User>
     */
    public function findAll(): array
    {
        $models = $this->model->newQuery()->get();

        return $models->map(function (EloquentUser $model) {
            return User::fromPersistence(
                $model->uuid,
                $model->restaurant_id,
                $model->role,
                $model->image_src,
                $model->name,
                $model->email,
                $model->password,
                $model->pin,
                $model->created_at->toDateTimeImmutable(),
                $model->updated_at->toDateTimeImmutable(),
            );
        })->all();
    }

    public function delete(User $user): void
    {
        $this->model->newQuery()->where('uuid', $user->id()->value())->delete();
    }
}