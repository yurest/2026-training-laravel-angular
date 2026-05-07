<?php

namespace App\Product\Infrastructure\Entrypoint\Http\Requests;

use App\Product\Application\GetProduct\GetProductCommand;
use Illuminate\Foundation\Http\FormRequest;

final class GetProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(string $id): GetProductCommand
    {
        return new GetProductCommand(
            id: $id,
        );
    }
}
