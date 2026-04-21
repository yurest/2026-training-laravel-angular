<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\UpdateUser\UpdateUser;
use App\User\Domain\Exception\UserNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateUser $updateUser,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['sometimes', 'string'],
            'image_src' => ['nullable', 'string', 'max:255'],
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255'],
            'pin' => ['sometimes', 'string', 'size:4'],
        ]);

        try {
            $response = ($this->updateUser)(
                $id,
                $validated['role'] ?? null,
                $validated['image_src'] ?? null,
                $validated['name'] ?? null,
                $validated['email'] ?? null,
                $validated['pin'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (UserNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
