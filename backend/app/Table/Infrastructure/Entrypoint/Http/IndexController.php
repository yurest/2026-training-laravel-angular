<?php

namespace App\Table\Infrastructure\Entrypoint\Http;

use App\Table\Application\GetTables\GetTables;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetTables $getTables,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getTables)();

        return new JsonResponse($response->toArray(), 200);
    }
}