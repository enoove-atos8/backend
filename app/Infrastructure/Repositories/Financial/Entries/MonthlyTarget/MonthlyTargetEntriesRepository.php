<?php

namespace Infrastructure\Repositories\Financial\Entries\MonthlyTarget;

use App\Domain\Financial\Entries\Consolidated\Models\ConsolidationEntries;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Financial\Entries\Indicators\MonthlyTarget\Interfaces\MonthlyTargetEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\BaseRepository;

class MonthlyTargetEntriesRepository extends BaseRepository implements MonthlyTargetEntriesRepositoryInterface
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
     * @throws BindingResolutionException
     */
    public function getHigherEntryAmount(string $amountType): Model
    {
        $selectColumns = [];

        if($amountType == 'tithe')
            $selectColumns = ['tithe_amount'];

        if($amountType == 'offers')
            $selectColumns = ['offers_amount'];

        if($amountType == 'designated')
            $selectColumns = ['designated_amount'];

        $conditions = [
                'field' => ConsolidationEntriesRepository::CONSOLIDATED_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => ConsolidationEntriesRepository::CONSOLIDATED_VALUE,
            ];

        return $this->getItemWithRelationshipsAndWheres
        (
            $conditions,
            'tithe_amount',
            $selectColumns,
            1
        );
    }
}
