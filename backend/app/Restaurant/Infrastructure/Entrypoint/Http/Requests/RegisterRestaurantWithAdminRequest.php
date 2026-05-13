<?php

namespace App\Restaurant\Infrastructure\Entrypoint\Http\Requests;

use App\Restaurant\Application\RegisterRestaurantWithAdmin\RegisterRestaurantWithAdminCommand;
use Illuminate\Foundation\Http\FormRequest;

final class RegisterRestaurantWithAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'restaurant_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:restaurants,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'pin' => ['sometimes', 'nullable', 'digits:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email is already registered.',
        ];
    }

    public function toCommand(): RegisterRestaurantWithAdminCommand
    {
        return new RegisterRestaurantWithAdminCommand(
            restaurantName: (string) $this->input('restaurant_name'),
            legalName: $this->input('legal_name'),
            taxId: $this->input('tax_id'),
            email: (string) $this->input('email'),
            plainPassword: (string) $this->input('password'),
            adminName: $this->input('admin_name'),
            adminPin: $this->input('pin'),
        );
    }
}
