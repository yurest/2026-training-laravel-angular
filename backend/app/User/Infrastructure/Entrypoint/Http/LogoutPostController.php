<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutPostController
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse(['message' => 'Logged out successfully'], 200);
    }
}