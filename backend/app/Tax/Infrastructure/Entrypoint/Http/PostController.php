<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\CreateTax\CreateTax;
use App\Tax\Domain\Exception\TaxNameAlreadyExistsException;
use App\Tax\Infrastructure\Entrypoint\Http\Requests\CreateTaxRequest;
use Illuminate\Http\JsonResponse;

class PostController
{
    public function __construct(
        private CreateTax $createTax,
    ) {}

    public function __invoke(CreateTaxRequest $request): JsonResponse
    {
        try {
            $response = ($this->createTax)($request->toCommand());
        } catch (TaxNameAlreadyExistsException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 409);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray(), 201);
    }
}
