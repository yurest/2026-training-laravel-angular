<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\GetRestaurant\GetRestaurant;
use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Infrastructure\Entrypoint\Http\Requests\GetRestaurantRequest;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private readonly GetRestaurant $getRestaurant,
    ) {}

    public function __invoke(GetRestaurantRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->getRestaurant)($request->toCommand($id));
        } catch (RestaurantNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
