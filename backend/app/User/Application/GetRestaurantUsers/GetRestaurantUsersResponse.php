<?php

namespace App\User\Application\GetRestaurantUsers;

final readonly class GetRestaurantUsersResponse
{
    /**
     * @param  array<array{uuid: string, name: string, email: string, role: string}>  $users
     */
    private function __construct(
        public array $users,
    ) {}

    /**
     * @param  array<array{uuid: string, name: string, email: string, role: string}>  $users
     */
    public static function create(array $users): self
    {
        return new self(
            users: $users,
        );
    }

    public function toArray(): array
    {
        return [
            'users' => $this->users,
        ];
    }
}
