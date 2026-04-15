<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\CreateUser\CreateUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PostController
{
    public function __construct(
        private CreateUser $createUser,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'role' => ['required', 'string'],
            'image_src' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'pin' => ['required', 'string', 'size:4'],
        ]);

        $response = ($this->createUser)(
            (string) $validated['restaurant_id'],
            $validated['role'],
            $validated['image_src'] ?? null,
            $validated['name'],
            $validated['email'],
            $validated['password'],
            $validated['pin'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}