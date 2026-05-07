<?php

namespace App\Tax\Infrastructure\Entrypoint\Http\Requests;

use App\Tax\Application\GetTax\GetTaxCommand;
use Illuminate\Foundation\Http\FormRequest;

final class GetTaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(string $id): GetTaxCommand
    {
        return new GetTaxCommand($id);
    }
}
