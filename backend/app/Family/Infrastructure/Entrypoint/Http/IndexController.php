<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\GetFamilies\GetFamilies;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetFamilies $getFamilies,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getFamilies)();

        return new JsonResponse($response->toArray(), 200);
    }
}
