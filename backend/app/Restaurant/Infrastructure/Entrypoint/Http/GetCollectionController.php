<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\ListRestaurants\ListRestaurants;
use App\Restaurant\Infrastructure\Entrypoint\Http\Requests\ListRestaurantsRequest;
use Illuminate\Http\JsonResponse;

final class GetCollectionController
{
    public function __construct(
        private readonly ListRestaurants $listRestaurants,
    ) {}

    public function __invoke(ListRestaurantsRequest $request): JsonResponse
    {
        try {
            $response = ($this->listRestaurants)();
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
