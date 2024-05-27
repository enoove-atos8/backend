<?php

namespace Infrastructure\Repositories\Financial\Entries\Indicators\TithesBalance;

use App\Domain\Financial\Entries\Consolidated\Models\ConsolidationEntries;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use Domain\Financial\Entries\Indicators\TithesBalance\Interfaces\TitheBalanceRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class TithesBalanceRepository extends BaseRepository implements TitheBalanceRepositoryInterface
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
    public function getLastConsolidatedEntriesTotalAmount(int $limit): Collection
    {
        $this->queryConditions = [];
        $selectColumns = ['date', 'tithe_amount'];

        $this->queryConditions [] = $this->whereEqual(ConsolidationEntriesRepository::CONSOLIDATED_COLUMN, ConsolidationEntriesRepository::CONSOLIDATED_VALUE, 'and');

        return $this->getItemsWithRelationshipsAndWheres
        (
            $this->queryConditions,
            ConsolidationEntriesRepository::DATE_COLUMN,
            BaseRepository::ORDERS['ASC'],
            $limit,
            $selectColumns
        );
    }
}
