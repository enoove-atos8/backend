<?php

namespace Domain\Financial\Entries\Indicators\TotalGeneral\Interfaces;

use Illuminate\Support\Collection;

interface TotalGeneralRepositoryInterface
{
    public function getTotalGeneralEntries(string|null $rangeMonthlyDate, array $filters): Collection;
}
