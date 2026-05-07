<?php

namespace App\OrderLine\Infrastructure\Entrypoint\Http;

use App\OrderLine\Application\DeleteOrderLine\DeleteOrderLine;
use App\OrderLine\Domain\Exception\OrderLineNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteOrderLine $deleteOrderLine,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteOrderLine)($id);

            return new JsonResponse(null, 204);
        } catch (OrderLineNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}