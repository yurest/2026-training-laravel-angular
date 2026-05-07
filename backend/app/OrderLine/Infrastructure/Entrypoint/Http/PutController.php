<?php

namespace App\OrderLine\Infrastructure\Entrypoint\Http;

use App\OrderLine\Application\UpdateOrderLine\UpdateOrderLine;
use App\OrderLine\Domain\Exception\OrderLineNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateOrderLine $updateOrderLine,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'price' => ['sometimes', 'integer', 'min:0'],
        ]);

        try {
            $response = ($this->updateOrderLine)(
                $id,
                $validated['quantity'] ?? null,
                $validated['price'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (OrderLineNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}