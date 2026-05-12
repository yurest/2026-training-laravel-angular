<?php

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\GetOpenOrders\GetOpenOrders;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OpenIndexController
{
    public function __construct(
        private GetOpenOrders $getOpenOrders,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
        ]);

        $response = ($this->getOpenOrders)(
            (string) $validated['restaurant_id'],
        );

        return new JsonResponse($response->toArray(), 200);
    }
}