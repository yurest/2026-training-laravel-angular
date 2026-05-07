<?php

namespace App\Family\Application\DeleteFamily;

use App\Family\Domain\Exception\FamilyNotFoundException;
use App\Family\Domain\Interfaces\FamilyRepositoryInterface;

class DeleteFamily
{
    public function __construct(
        private FamilyRepositoryInterface $familyRepository,
    ) {}

    public function __invoke(DeleteFamilyCommand $command): void
    {
        $family = $this->familyRepository->findById($command->id)
            ?? throw FamilyNotFoundException::withId($command->id);

        $this->familyRepository->deleteById($family->id()->value());
    }
}
