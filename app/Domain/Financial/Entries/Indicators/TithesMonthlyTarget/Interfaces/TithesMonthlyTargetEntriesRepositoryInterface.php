<?php

namespace Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Interfaces;

use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\DataTransferObjects\TithesMonthlyTargetEntriesData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface TithesMonthlyTargetEntriesRepositoryInterface
{
    public function getLastConsolidatedTitheEntries(int $limit): Collection;
}
