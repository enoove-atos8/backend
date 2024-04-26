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
     * Array of where, between and another clauses that was mounted dynamically
     */
    private array $queryClausesAndConditions = [
        'where_clause'    =>  [
            'exists' => false,
            'clause'   =>  [],
        ]
    ];

    /**
     * @param int $limit
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getLastConsolidatedTitheEntries(int $limit): Collection
    {
        $selectColumns = ['date', 'tithe_amount'];
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => ConsolidationEntriesRepository::CONSOLIDATED_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => ConsolidationEntriesRepository::CONSOLIDATED_VALUE,
            ]
        ];

        return $this->getItemsWithRelationshipsAndWheres
        (
            $this->queryClausesAndConditions,
            ConsolidationEntriesRepository::DATE_COLUMN,
            BaseRepository::ORDERS['ASC'],
            $limit,
            $selectColumns
        );
    }
}
