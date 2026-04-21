<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\UpdateZone\UpdateZone;
use App\Zone\Domain\Exception\ZoneNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateZone $updateZone,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
        ]);

        try {
            $response = ($this->updateZone)(
                $id,
                $validated['name'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (ZoneNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
