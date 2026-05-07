<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\UpdateFamily\UpdateFamily;
use App\Family\Domain\Exception\FamilyNotFoundException;
use App\Family\Infrastructure\Entrypoint\Http\Requests\UpdateFamilyRequest;
use Illuminate\Http\JsonResponse;

final class PutController
{
    public function __construct(
        private UpdateFamily $updateFamily,
    ) {}

    public function __invoke(UpdateFamilyRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->updateFamily)($request->toCommand($id));
        } catch (FamilyNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
