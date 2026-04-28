<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\DeleteZone\DeleteZone;
use App\Zone\Domain\Exception\ZoneNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteZone $deleteZone,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteZone)($id);

            return new JsonResponse(null, 204);
        } catch (ZoneNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
