<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\DeleteRestaurant\DeleteRestaurant;
use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteRestaurant $deleteRestaurant,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteRestaurant)($id);

            return new JsonResponse(null, 204);
        } catch (RestaurantNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
