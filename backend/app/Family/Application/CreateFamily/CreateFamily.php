<?php

namespace App\Family\Application\CreateFamily;

use App\Family\Domain\Entity\Family;
use App\Family\Domain\Interfaces\FamilyRepositoryInterface;
use App\Family\Domain\ValueObject\FamilyName;

class CreateFamily
{
    public function __construct(
        private FamilyRepositoryInterface $familyRepository,
    ) {}

    public function __invoke(CreateFamilyCommand $command): CreateFamilyResponse
    {
        $family = Family::dddCreate(FamilyName::create($command->name));
        $this->familyRepository->save($family);

        return CreateFamilyResponse::create(
            id: $family->id()->value(),
            name: $family->name()->value(),
            active: $family->isActive(),
            createdAt: $family->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $family->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
