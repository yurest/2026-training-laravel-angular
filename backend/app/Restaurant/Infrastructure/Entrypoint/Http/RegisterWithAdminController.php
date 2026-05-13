<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\RegisterRestaurantWithAdmin\RegisterRestaurantWithAdmin;
use App\Restaurant\Infrastructure\Entrypoint\Http\Requests\RegisterRestaurantWithAdminRequest;
use Illuminate\Http\JsonResponse;

final class RegisterWithAdminController
{
    public function __construct(
        private readonly RegisterRestaurantWithAdmin $registerRestaurantWithAdmin,
    ) {}

    public function __invoke(RegisterRestaurantWithAdminRequest $request): JsonResponse
    {
        try {
            $response = ($this->registerRestaurantWithAdmin)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray(), 201);
    }
}
