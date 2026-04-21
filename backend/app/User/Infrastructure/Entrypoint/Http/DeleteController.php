<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\DeleteUser\DeleteUser;
use App\User\Domain\Exception\UserNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteUser $deleteUser,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteUser)($id);

            return new JsonResponse(null, 204);
        } catch (UserNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
