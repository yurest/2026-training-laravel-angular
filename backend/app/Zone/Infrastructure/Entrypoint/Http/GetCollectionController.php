<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\ListZones\ListZones;
use App\Zone\Infrastructure\Entrypoint\Http\Requests\ListZonesRequest;
use Illuminate\Http\JsonResponse;

final class GetCollectionController
{
    public function __construct(
        private ListZones $listZones,
    ) {}

    public function __invoke(ListZonesRequest $request): JsonResponse
    {
        try {
            $response = ($this->listZones)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
