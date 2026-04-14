<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\GetZone\GetZone;
use App\Zone\Domain\Exception\ZoneNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetZone $getZone,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getZone)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (ZoneNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}