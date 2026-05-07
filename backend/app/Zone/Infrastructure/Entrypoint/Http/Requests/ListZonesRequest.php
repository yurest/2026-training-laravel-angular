<?php

namespace App\Zone\Infrastructure\Entrypoint\Http\Requests;

use App\Zone\Application\ListZones\ListZonesCommand;
use Illuminate\Foundation\Http\FormRequest;

final class ListZonesRequest extends FormRequest
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

    public function toCommand(): ListZonesCommand
    {
        return new ListZonesCommand(
            includeDeleted: $this->query('all') === 'true',
        );
    }
}
