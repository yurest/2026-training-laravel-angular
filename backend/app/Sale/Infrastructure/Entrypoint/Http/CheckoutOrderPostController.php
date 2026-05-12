<?php

namespace App\Sale\Infrastructure\Entrypoint\Http;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Sale\Application\CheckoutOrder\CheckoutOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CheckoutOrderPostController
{
    public function __construct(
        private CheckoutOrder $checkoutOrder,
    ) {}

    public function __invoke(Request $request, string $orderId): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'user_id' => ['required', 'string'],
        ]);

        try {
            $response = ($this->checkoutOrder)(
                (string) $validated['restaurant_id'],
                $orderId,
                $validated['user_id'],
            );

            return new JsonResponse($response->toArray(), 201);
        } catch (OrderNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}