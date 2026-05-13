<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\DeleteRestaurant\DeleteRestaurant;
use App\Restaurant\Domain\Exception\NotSuperAdminException;
use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Infrastructure\Entrypoint\Http\Requests\DeleteRestaurantRequest;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private readonly DeleteRestaurant $deleteRestaurant,
    ) {}

    public function __invoke(DeleteRestaurantRequest $request, string $id): JsonResponse
    {
        try {
            ($this->deleteRestaurant)($request->toCommand($id));
        } catch (NotSuperAdminException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        } catch (RestaurantNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse(status: 204);
    }
}
