<?php

namespace App\Tables\Infrastructure\Entrypoint\Http;

use App\Tables\Application\UpdateTable\UpdateTable;
use App\Tables\Domain\Exception\TableNameAlreadyExistsInZoneException;
use App\Tables\Domain\Exception\TableNotFoundException;
use App\Tables\Infrastructure\Entrypoint\Http\Requests\UpdateTableRequest;
use Illuminate\Http\JsonResponse;

final class PutController
{
    public function __construct(
        private UpdateTable $updateTable,
    ) {}

    public function __invoke(UpdateTableRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->updateTable)($request->toCommand($id));
        } catch (TableNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (TableNameAlreadyExistsInZoneException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 409);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
