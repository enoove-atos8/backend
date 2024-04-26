<?php

namespace Domain\Financial\Entries\Indicators\TithesBalance\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface TitheBalanceRepositoryInterface
{
    public function getLastConsolidatedEntriesTotalAmount(int $limit): Collection;
}
