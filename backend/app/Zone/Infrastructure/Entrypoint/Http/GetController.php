<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\GetZone\GetZone;
use App\Zone\Domain\Exception\ZoneNotFoundException;
use App\Zone\Infrastructure\Entrypoint\Http\Requests\GetZoneRequest;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetZone $getZone,
    ) {}

    public function __invoke(GetZoneRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->getZone)($request->toCommand($id));
        } catch (ZoneNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
