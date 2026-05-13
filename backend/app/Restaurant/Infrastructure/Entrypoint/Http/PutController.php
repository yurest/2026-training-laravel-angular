<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http;

use App\Restaurant\Application\UpdateRestaurant\UpdateRestaurant;
use App\Restaurant\Domain\Exception\CannotUpdateLegalDataException;
use App\Restaurant\Domain\Exception\ForbiddenException;
use App\Restaurant\Domain\Exception\LinkedRestaurantNotFoundException;
use App\Restaurant\Domain\Exception\NotAuthenticatedException;
use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Infrastructure\Entrypoint\Http\Requests\UpdateRestaurantRequest;
use Illuminate\Http\JsonResponse;

final class PutController
{
    public function __construct(
        private readonly UpdateRestaurant $updateRestaurant,
    ) {}

    public function __invoke(UpdateRestaurantRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->updateRestaurant)($request->toCommand($id));
        } catch (NotAuthenticatedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        } catch (LinkedRestaurantNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (RestaurantNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (CannotUpdateLegalDataException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
