<?php

namespace App\Table\Infrastructure\Entrypoint\Http;

use App\Table\Application\GetTable\GetTable;
use App\Table\Domain\Exception\TableNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetTable $getTable,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getTable)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (TableNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}