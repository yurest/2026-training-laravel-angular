<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\UpdateZone\UpdateZone;
use App\Zone\Domain\Exception\ZoneNotFoundException;
use App\Zone\Infrastructure\Entrypoint\Http\Requests\UpdateZoneRequest;
use Illuminate\Http\JsonResponse;

final class PutController
{
    public function __construct(
        private UpdateZone $updateZone,
    ) {}

    public function __invoke(UpdateZoneRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->updateZone)($request->toCommand($id));
        } catch (ZoneNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
