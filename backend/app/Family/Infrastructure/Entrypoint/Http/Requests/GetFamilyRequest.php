<?php

namespace App\Family\Infrastructure\Entrypoint\Http\Requests;

use App\Family\Application\GetFamily\GetFamilyCommand;
use Illuminate\Foundation\Http\FormRequest;

final class GetFamilyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(string $id): GetFamilyCommand
    {
        return new GetFamilyCommand($id);
    }
}
