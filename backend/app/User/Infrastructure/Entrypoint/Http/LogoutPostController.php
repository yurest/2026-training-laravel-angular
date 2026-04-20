<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\LogoutUser\LogoutUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutPostController
{
    public function __construct(
        private LogoutUser $logoutUser,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if ($token === null) {
            return new JsonResponse(['message' => 'Unauthenticated.'], 401);
        }

        ($this->logoutUser)($token);

        return new JsonResponse(['message' => 'Logged out successfully'], 200);
    }
}
