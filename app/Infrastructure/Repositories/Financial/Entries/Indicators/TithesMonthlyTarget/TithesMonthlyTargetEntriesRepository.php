<?php

namespace App\Infrastructure\Repositories\Financial\Entries\Indicators\TithesMonthlyTarget;

use App\Domain\Financial\Entries\Consolidation\Models\Consolidation;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Interfaces\TithesMonthlyTargetEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class TithesMonthlyTargetEntriesRepository extends BaseRepository implements TithesMonthlyTargetEntriesRepositoryInterface
{
    protected mixed $model = Consolidation::class;
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

        $this->queryConditions [] = $this->whereEqual(ConsolidationRepository::CONSOLIDATED_COLUMN, ConsolidationRepository::CONSOLIDATED_VALUE, 'and');

        return $this->getItemsWithRelationshipsAndWheres
        (
            $this->queryConditions,
            ConsolidationRepository::DATE_COLUMN,
            BaseRepository::ORDERS['DESC'],
            $limit,
            $selectColumns
        );
    }
}
