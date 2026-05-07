<?php

namespace App\Sale\Infrastructure\Entrypoint\Http;

use App\Sale\Application\GetSales\GetSales;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class IndexController
{
    public function __construct(
        private GetSales $getSales,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'date' => ['nullable', 'date'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'shift' => ['nullable', 'string', 'in:comida,cena'],
        ]);

        $response = ($this->getSales)(
            (string) $validated['restaurant_id'],
            $validated['date'] ?? null,
            $validated['from'] ?? null,
            $validated['to'] ?? null,
            $validated['shift'] ?? null,
        );

        return new JsonResponse($response->toArray(), 200);
    }
}