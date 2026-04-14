<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\GetZones\GetZones;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetZones $getZones,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getZones)();

        return new JsonResponse($response->toArray(), 200);
    }
}