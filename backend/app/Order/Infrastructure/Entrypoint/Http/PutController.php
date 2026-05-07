<?php

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\UpdateOrder\UpdateOrder;
use App\Order\Domain\Exception\OrderNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateOrder $updateOrder,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string'],
            'table_id' => ['sometimes', 'integer'],
            'closed_by_user_id' => ['nullable', 'integer'],
            'diners' => ['sometimes', 'integer', 'min:1'],
            'closed_at' => ['nullable', 'date'],
        ]);

        try {
            $response = ($this->updateOrder)(
                $id,
                $validated['status'] ?? null,
                isset($validated['table_id']) ? (string) $validated['table_id'] : null,
                isset($validated['closed_by_user_id']) ? (string) $validated['closed_by_user_id'] : null,
                $validated['diners'] ?? null,
                $validated['closed_at'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (OrderNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}