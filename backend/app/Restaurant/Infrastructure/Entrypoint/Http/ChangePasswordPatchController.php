<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\ChangeRestaurantPassword\ChangeRestaurantPassword;
use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ChangePasswordPatchController
{
    public function __construct(
        private ChangeRestaurantPassword $changeRestaurantPassword,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        try {
            $response = ($this->changeRestaurantPassword)(
                $id,
                $validated['password'],
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (RestaurantNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
