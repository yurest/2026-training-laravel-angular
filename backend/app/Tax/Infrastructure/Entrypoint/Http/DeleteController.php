<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\DeleteTax\DeleteTax;
use App\Tax\Domain\Exception\TaxNotFoundException;
use App\Tax\Infrastructure\Entrypoint\Http\Requests\DeleteTaxRequest;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteTax $deleteTax,
    ) {}

    public function __invoke(DeleteTaxRequest $request, string $id): JsonResponse
    {
        try {
            ($this->deleteTax)($request->toCommand($id));
        } catch (TaxNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse(null, 204);
    }
}
