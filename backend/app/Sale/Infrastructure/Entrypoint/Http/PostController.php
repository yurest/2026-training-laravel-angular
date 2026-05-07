<?php

namespace App\Sale\Infrastructure\Entrypoint\Http;

use App\Sale\Application\CreateSale\CreateSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PostController
{
    public function __construct(
        private CreateSale $createSale,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'order_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'total' => ['required', 'integer', 'min:0'],
        ]);

        $response = ($this->createSale)(
            (string) $validated['restaurant_id'],
            (string) $validated['order_id'],
            (string) $validated['user_id'],
            $validated['total'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}