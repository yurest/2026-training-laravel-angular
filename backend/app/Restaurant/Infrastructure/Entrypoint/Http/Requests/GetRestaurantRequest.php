<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http\Requests;

use App\Restaurant\Application\GetRestaurant\GetRestaurantCommand;
use Illuminate\Foundation\Http\FormRequest;

final class GetRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(string $id): GetRestaurantCommand
    {
        return new GetRestaurantCommand(
            id: $id,
        );
    }
}
