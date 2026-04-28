<?php

namespace App\Family\Application\GetFamilies;

use App\Family\Application\GetFamily\GetFamilyResponse;

final readonly class GetFamiliesResponse
{
    /**
     * @param  GetFamilyResponse[]  $families
     */
    public function __construct(
        public array $families,
    ) {}

    /**
     * @param  GetFamilyResponse[]  $families
     */
    public static function create(array $families): self
    {
        return new self($families);
    }

    /**
     * @return array<string, array<int, array<string, string|bool>>>
     */
    public function toArray(): array
    {
        return [
            'families' => array_map(
                fn (GetFamilyResponse $family) => $family->toArray(),
                $this->families,
            ),
        ];
    }
}
