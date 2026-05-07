<?php

namespace App\Zone\Infrastructure\Entrypoint\Http\Requests;

use App\Zone\Application\GetZone\GetZoneCommand;
use Illuminate\Foundation\Http\FormRequest;

final class GetZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function toCommand(string $id): GetZoneCommand
    {
        return new GetZoneCommand($id);
    }
}
