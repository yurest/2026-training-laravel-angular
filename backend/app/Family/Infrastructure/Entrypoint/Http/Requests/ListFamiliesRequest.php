<?php

namespace App\Family\Infrastructure\Entrypoint\Http\Requests;

use App\Family\Application\ListFamilies\ListFamiliesCommand;
use Illuminate\Foundation\Http\FormRequest;

final class ListFamiliesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'all' => ['nullable', 'boolean'],
        ];
    }

    public function toCommand(): ListFamiliesCommand
    {
        return new ListFamiliesCommand(
            includeDeleted: $this->query('all') === 'true',
            onlyActive: false,
        );
    }
}
