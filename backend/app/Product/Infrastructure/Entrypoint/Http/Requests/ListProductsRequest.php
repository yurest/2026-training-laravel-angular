<?php

namespace App\Product\Infrastructure\Entrypoint\Http\Requests;

use App\Product\Application\ListProducts\ListProductsCommand;
use Illuminate\Foundation\Http\FormRequest;

final class ListProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'all' => ['sometimes', 'boolean'],
            'only_active' => ['sometimes', 'boolean'],
        ];
    }

    public function toCommand(): ListProductsCommand
    {
        return new ListProductsCommand(
            includeDeleted: $this->query('all') === 'true',
            onlyActive: $this->query('only_active') === 'true',
        );
    }
}
