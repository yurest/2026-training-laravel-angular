<?php

namespace App\Family\Application\GetFamilies;

use App\Family\Application\GetFamily\GetFamilyResponse;
use App\Family\Domain\Interfaces\FamilyRepositoryInterface;

final class GetFamilies
{
    public function __construct(
        private FamilyRepositoryInterface $familyRepository,
    ) {}

    public function __invoke(): GetFamiliesResponse
    {
        $families = $this->familyRepository->findAll();

        $familyResponses = array_map(
            fn ($family) => GetFamilyResponse::create($family),
            $families,
        );

        return GetFamiliesResponse::create($familyResponses);
    }
}
