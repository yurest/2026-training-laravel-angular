<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\ListTaxes\ListTaxes;
use App\Tax\Infrastructure\Entrypoint\Http\Requests\ListTaxesRequest;
use Illuminate\Http\JsonResponse;

final class GetCollectionController
{
    public function __construct(
        private ListTaxes $listTaxes,
    ) {}

    public function __invoke(ListTaxesRequest $request): JsonResponse
    {
        try {
            $response = ($this->listTaxes)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
