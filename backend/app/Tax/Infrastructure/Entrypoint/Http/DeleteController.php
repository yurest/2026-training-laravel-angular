<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\DeleteTax\DeleteTax;
use App\Tax\Domain\Exception\TaxNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteTax $deleteTax,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteTax)($id);

            return new JsonResponse(null, 204);
        } catch (TaxNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
