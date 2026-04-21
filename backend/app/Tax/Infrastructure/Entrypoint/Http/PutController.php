<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\UpdateTax\UpdateTax;
use App\Tax\Domain\Exception\TaxNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateTax $updateTax,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'percentage' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ]);

        try {
            $response = ($this->updateTax)(
                $id,
                $validated['name'] ?? null,
                $validated['percentage'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (TaxNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
