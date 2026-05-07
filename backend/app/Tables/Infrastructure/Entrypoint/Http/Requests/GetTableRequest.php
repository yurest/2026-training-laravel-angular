<?php

namespace App\Tables\Infrastructure\Entrypoint\Http\Requests;

use App\Tables\Application\GetTable\GetTableCommand;
use Illuminate\Foundation\Http\FormRequest;

final class GetTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(string $id): GetTableCommand
    {
        return new GetTableCommand($id);
    }
}
