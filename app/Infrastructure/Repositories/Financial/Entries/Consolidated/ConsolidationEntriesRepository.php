<?php

namespace App\Infrastructure\Repositories\Financial\Entries\Consolidated;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\Consolidated\Models\ConsolidationEntries;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class ConsolidationEntriesRepository extends BaseRepository implements ConsolidatedEntriesRepositoryInterface
{
    protected mixed $model = ConsolidationEntries::class;

    const DATE_COLUMN = 'date';
    const CONSOLIDATED_COLUMN = 'consolidated';
    const NOT_CONSOLIDATED_VALUE = '0';
    const CONSOLIDATED_VALUE = '1';
    const AMOUNT_TITHE_COLUMN = 'tithe_amount';
    const AMOUNT_DESIGNATED_COLUMN = 'designated_amount';
    const AMOUNT_OFFERS_COLUMN = 'offers_amount';
    const AMOUNT_TOTAL_COLUMN = 'total_amount';

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
     * @param string $date
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getByDate(string $date): Model|null
    {
        $this->requiredRelationships = [];
        return $this->getItemByColumn(self::DATE_COLUMN, $date);
    }


    /**
     * @param ConsolidationEntriesData $consolidationEntriesData
     * @return void
     */
    public function new(ConsolidationEntriesData $consolidationEntriesData): void
    {
        $this->create([
            'date'          =>  substr($consolidationEntriesData->date, 0, 7),
            'consolidated'  =>  $consolidationEntriesData->consolidated,
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getConsolidatedEntriesByStatus(string $consolidated = 'all', int $limit = 6, string $orderDirection = 'DESC'): Collection
    {
        $currentYearMonth = date("Y-m");
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        if($consolidated != 'all')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] = [
                'type' => 'and',
                'condition' => ['field' => self::CONSOLIDATED_COLUMN, 'operator' =>
                    BaseRepository::OPERATORS['EQUALS'], 'value' => $consolidated,]
            ];
        }

        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'not_in',
            'condition' => ['field' => self::DATE_COLUMN, 'operator' =>
                BaseRepository::OPERATORS['EQUALS'], 'value' => $currentYearMonth,]
        ];

        return $this->getItemsWithRelationshipsAndWheres(
                                $this->queryClausesAndConditions,
                                self::DATE_COLUMN,
                                $orderDirection,
                                $limit);
    }



    /**
     * @throws BindingResolutionException
     */
    public function getEntriesEvolutionConsolidation(string $consolidatedValues, int $limit = 6): Collection
    {
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        if($consolidatedValues != '*')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] = [
                'type' => 'and',
                'condition' => ['field' => ConsolidationEntriesRepository::CONSOLIDATED_COLUMN,
                    'operator' => BaseRepository::OPERATORS['EQUALS'],
                    'value' => $consolidatedValues,]
            ];
        }

        return $this->getItemsWithRelationshipsAndWheres(
            $this->queryClausesAndConditions,
            self::DATE_COLUMN,
            BaseRepository::ORDERS['ASC'],
            $limit);
    }


    /**
     * @param array $dates
     * @param string $status
     * @return bool
     * @throws BindingResolutionException
     */
    public function updateConsolidationStatus(array $dates, string $status): bool
    {
        foreach ($dates as $date)
        {
            $this->update([
                'field' =>  'date',
                'operator'  =>  '=',
                'value' =>  $date
            ], ['consolidated'  =>  $status]);
        }

        return true;
    }


    /**
     * @param string $date
     * @param string $amount
     * @param string $column
     * @return bool
     * @throws BindingResolutionException
     */
    public function updateTotalValueConsolidation(string $date, string $amount, string $column): bool
    {
        return $this->update([
            'field' =>  'date',
            'operator'  =>  '=',
            'value' =>  $date
        ], [$column  =>  $amount]);
    }


    /**
     * @param string $date
     * @return bool
     */
    public function deleteConsolidationEntry(string $date): bool
    {
        return $this->deleteByColumn(self::DATE_COLUMN, $date);
    }
}
