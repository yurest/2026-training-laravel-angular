<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\GetRestaurant\GetRestaurant;
use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetRestaurant $getRestaurant,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getRestaurant)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (RestaurantNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
