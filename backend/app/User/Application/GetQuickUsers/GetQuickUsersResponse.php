<?php

namespace App\User\Application\GetQuickUsers;

final readonly class GetQuickUsersResponse
{
    /**
     * @param  array<int, array<string, mixed>>  $users
     */
    private function __construct(private array $users) {}

    /**
     * @param  array<int, array<string, mixed>>  $users
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
