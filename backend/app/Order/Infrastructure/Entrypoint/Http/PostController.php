<?php

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\CreateOrder\CreateOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PostController
{
    public function __construct(
        private CreateOrder $createOrder,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'table_id' => ['required', 'integer'],
            'opened_by_user_id' => ['required', 'integer'],
            'diners' => ['required', 'integer', 'min:1'],
        ]);

        $response = ($this->createOrder)(
            (string) $validated['restaurant_id'],
            (string) $validated['table_id'],
            (string) $validated['opened_by_user_id'],
            (int) $validated['diners'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}