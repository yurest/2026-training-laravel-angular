<?php

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\DeleteOrder\DeleteOrder;
use App\Order\Domain\Exception\OrderNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteOrder $deleteOrder,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteOrder)($id);

            return new JsonResponse(null, 204);
        } catch (OrderNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}