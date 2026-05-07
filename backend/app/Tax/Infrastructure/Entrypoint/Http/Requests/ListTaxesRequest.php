<?php

namespace App\Tax\Infrastructure\Entrypoint\Http\Requests;

use App\Tax\Application\ListTaxes\ListTaxesCommand;
use Illuminate\Foundation\Http\FormRequest;

final class ListTaxesRequest extends FormRequest
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

    public function toCommand(): ListTaxesCommand
    {
        return new ListTaxesCommand(
            includeDeleted: $this->query('all') === 'true',
        );
    }
}
