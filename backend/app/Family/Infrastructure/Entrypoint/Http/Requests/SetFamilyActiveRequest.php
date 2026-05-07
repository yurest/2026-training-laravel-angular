<?php

namespace App\Family\Infrastructure\Entrypoint\Http\Requests;

use App\Family\Application\SetFamilyActive\SetFamilyActiveCommand;
use Illuminate\Foundation\Http\FormRequest;

final class SetFamilyActiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(string $id, bool $active): SetFamilyActiveCommand
    {
        return new SetFamilyActiveCommand(
            id: $id,
            active: $active,
        );
    }
}
