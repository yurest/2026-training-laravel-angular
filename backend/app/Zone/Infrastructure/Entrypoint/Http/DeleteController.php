<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\DeleteZone\DeleteZone;
use App\Zone\Domain\Exception\ZoneNotFoundException;
use App\Zone\Infrastructure\Entrypoint\Http\Requests\DeleteZoneRequest;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteZone $deleteZone,
    ) {}

    public function __invoke(DeleteZoneRequest $request, string $id): JsonResponse
    {
        try {
            ($this->deleteZone)($request->toCommand($id));
        } catch (ZoneNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse(null, 204);
    }
}
