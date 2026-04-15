<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\GetUser\GetUser;
use App\User\Domain\Exception\UserNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetUser $getUser,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getUser)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (UserNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}