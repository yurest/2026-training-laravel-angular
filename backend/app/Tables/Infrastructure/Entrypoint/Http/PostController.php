<?php

namespace App\Tables\Infrastructure\Entrypoint\Http;

use App\Tables\Application\CreateTable\CreateTable;
use App\Tables\Domain\Exception\TableNameAlreadyExistsInZoneException;
use App\Tables\Infrastructure\Entrypoint\Http\Requests\CreateTableRequest;
use Illuminate\Http\JsonResponse;

final class PostController
{
    public function __construct(
        private CreateTable $createTable,
    ) {}

    public function __invoke(CreateTableRequest $request): JsonResponse
    {
        try {
            $response = ($this->createTable)($request->toCommand());
        } catch (TableNameAlreadyExistsInZoneException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 409);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray(), 201);
    }
}
