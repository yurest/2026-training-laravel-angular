<?php

namespace App\Tax\Application\ListTaxes;

use App\Tax\Domain\Interfaces\TaxRepositoryInterface;

class ListTaxes
{
    public function __construct(
        private TaxRepositoryInterface $taxRepository,
    ) {}

    public function __invoke(ListTaxesCommand $command): ListTaxesResponse
    {
        $taxes = $this->taxRepository->findAll($command->includeDeleted ?? false);

        $items = array_map(
            static fn ($tax): array => [
                'id' => $tax->id()->value(),
                'name' => $tax->name()->value(),
                'percentage' => $tax->percentage()->value(),
                'created_at' => $tax->createdAt()->format(\DateTimeInterface::ATOM),
                'updated_at' => $tax->updatedAt()->format(\DateTimeInterface::ATOM),
            ],
            $taxes,
        );

        return ListTaxesResponse::create($items);
    }
}
