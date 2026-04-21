<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\UpdateFamily\UpdateFamily;
use App\Family\Domain\Exception\FamilyNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateFamily $updateFamily,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'active' => ['sometimes', 'boolean'],
        ]);

        try {
            $response = ($this->updateFamily)(
                $id,
                $validated['name'] ?? null,
                $validated['active'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (FamilyNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
