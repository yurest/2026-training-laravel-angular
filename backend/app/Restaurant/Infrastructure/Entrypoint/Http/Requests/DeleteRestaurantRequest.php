<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http\Requests;

use App\Restaurant\Application\DeleteRestaurant\DeleteRestaurantCommand;
use Illuminate\Foundation\Http\FormRequest;

final class DeleteRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(string $id): DeleteRestaurantCommand
    {
        $superAdminUuid = $this->session()->get('super_admin_id');

        return new DeleteRestaurantCommand(
            id: $id,
            superAdminUuid: is_string($superAdminUuid) ? $superAdminUuid : null,
        );
    }
}
