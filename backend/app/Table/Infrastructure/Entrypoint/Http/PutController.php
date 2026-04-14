<?php

namespace App\Table\Infrastructure\Entrypoint\Http;

use App\Table\Application\UpdateTable\UpdateTable;
use App\Table\Domain\Exception\TableNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateTable $updateTable,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'zone_id' => ['sometimes', 'integer'],
            'name' => ['sometimes', 'string', 'max:255'],
        ]);

        try {
            $response = ($this->updateTable)(
                $id,
                isset($validated['zone_id']) ? (string) $validated['zone_id'] : null,
                $validated['name'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (TableNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}