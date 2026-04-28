<?php

namespace App\User\Application\GetUsers;

use App\User\Application\GetUser\GetUserResponse;

final readonly class GetUsersResponse
{
    /**
     * @param  GetUserResponse[]  $users
     */
    public function __construct(
        public array $users,
    ) {}

    /**
     * @param  GetUserResponse[]  $users
     */
    public static function create(array $users): self
    {
        return new self($users);
    }

    /**
     * @return array<string, array<int, array<string, string|null>>>
     */
    public function toArray(): array
    {
        return [
            'users' => array_map(
                fn (GetUserResponse $user) => $user->toArray(),
                $this->users,
            ),
        ];
    }
}
