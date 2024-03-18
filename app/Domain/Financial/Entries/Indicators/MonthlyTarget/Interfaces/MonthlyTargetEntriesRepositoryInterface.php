<?php

namespace Domain\Financial\Entries\Indicators\MonthlyTarget\Interfaces;

use Domain\Financial\Entries\Indicators\MonthlyTarget\DataTransferObjects\MonthlyTargetEntriesData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface MonthlyTargetEntriesRepositoryInterface
{
    public function getHigherEntryAmount(string $amountType): Model;
}
