<?php

namespace App\OrderLine\Infrastructure\Entrypoint\Http;

use App\OrderLine\Application\CreateOrderLine\CreateOrderLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PostController
{
    public function __construct(
        private CreateOrderLine $createOrderLine,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'order_id' => ['required', 'string'],
            'product_id' => ['required', 'string'],
            'user_id' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'integer', 'min:0'],
            'tax_percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $response = ($this->createOrderLine)(
            (string) $validated['restaurant_id'],
            $validated['order_id'],
            $validated['product_id'],
            $validated['user_id'],
            $validated['quantity'],
            $validated['price'],
            $validated['tax_percentage'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}