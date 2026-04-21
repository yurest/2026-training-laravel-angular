<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\GetRestaurants\GetRestaurants;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetRestaurants $getRestaurants,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getRestaurants)();

        return new JsonResponse($response->toArray(), 200);
    }
}
