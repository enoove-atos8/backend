<?php

namespace Domain\Financial\Entries\Indicators\AmountDevolutions\Interfaces;

use Illuminate\Support\Collection;

interface AmountDevolutionRepositoryInterface
{
    public function getDevolutionEntriesAmount(): Collection;
}
