<?php

declare(strict_types=1);

namespace App\Family\Application\ListActiveFamilies;

use App\Family\Application\ListFamilies\ListFamilies;
use App\Family\Application\ListFamilies\ListFamiliesCommand;
use App\Family\Application\ListFamilies\ListFamiliesResponse;

final class ListActiveFamilies
{
    public function __construct(
        private ListFamilies $listFamilies,
    ) {}

    public function __invoke(ListActiveFamiliesCommand $command): ListFamiliesResponse
    {
        return ($this->listFamilies)(
            new ListFamiliesCommand(
                includeDeleted: false,
                onlyActive: true,
            ),
        );
    }
}
