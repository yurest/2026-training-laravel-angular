<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\GetFamily\GetFamily;
use App\Family\Domain\Exception\FamilyNotFoundException;
use App\Family\Infrastructure\Entrypoint\Http\Requests\GetFamilyRequest;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetFamily $getFamily,
    ) {}

    public function __invoke(GetFamilyRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->getFamily)($request->toCommand($id));
        } catch (FamilyNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
