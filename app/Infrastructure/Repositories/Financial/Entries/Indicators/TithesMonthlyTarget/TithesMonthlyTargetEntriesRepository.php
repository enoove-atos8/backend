<?php

namespace App\Infrastructure\Repositories\Financial\Entries\Indicators\TithesMonthlyTarget;

use App\Domain\Financial\Entries\Consolidated\Models\ConsolidationEntries;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Interfaces\TithesMonthlyTargetEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class TithesMonthlyTargetEntriesRepository extends BaseRepository implements TithesMonthlyTargetEntriesRepositoryInterface
{
    protected mixed $model = ConsolidationEntries::class;
    const TABLE_NAME = 'consolidation_entries';


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param int $limit
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getLastConsolidatedTitheEntries(int $limit): Collection
    {
        $selectColumns = ['date', 'tithe_amount'];
        $this->queryConditions  = [];

        $this->queryConditions [] = $this->whereEqual(ConsolidationEntriesRepository::CONSOLIDATED_COLUMN, ConsolidationEntriesRepository::CONSOLIDATED_VALUE, 'and');

        return $this->getItemsWithRelationshipsAndWheres
        (
            $this->queryConditions,
            ConsolidationEntriesRepository::DATE_COLUMN,
            BaseRepository::ORDERS['DESC'],
            $limit,
            $selectColumns
        );
    }
}
