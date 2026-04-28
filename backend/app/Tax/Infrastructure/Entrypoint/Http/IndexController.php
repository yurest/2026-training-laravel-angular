<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\GetTaxes\GetTaxes;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetTaxes $getTaxes,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getTaxes)();

        return new JsonResponse($response->toArray(), 200);
    }
}
