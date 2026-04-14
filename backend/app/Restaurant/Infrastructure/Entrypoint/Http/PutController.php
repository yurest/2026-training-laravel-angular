<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\UpdateRestaurant\UpdateRestaurant;
use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateRestaurant $updateRestaurant,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'legal_name' => ['sometimes', 'string', 'max:255'],
            'tax_id' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
        ]);

        try {
            $response = ($this->updateRestaurant)(
                $id,
                $validated['name'] ?? null,
                $validated['legal_name'] ?? null,
                $validated['tax_id'] ?? null,
                $validated['email'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (RestaurantNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
