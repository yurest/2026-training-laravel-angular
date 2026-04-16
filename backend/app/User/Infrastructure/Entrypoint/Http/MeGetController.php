<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\GetUser\GetUser;
use App\User\Domain\Exception\UserNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MeGetController
{
    public function __construct(
        private GetUser $getUser,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $authUser = $request->user();

        if ($authUser === null) {
            return new JsonResponse(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $response = ($this->getUser)($authUser->uuid);

            return new JsonResponse($response->toArray(), 200);
        } catch (UserNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
