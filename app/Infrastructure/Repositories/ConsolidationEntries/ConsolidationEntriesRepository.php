<?php

namespace Infrastructure\Repositories\ConsolidationEntries;

use Domain\ConsolidationEntries\DataTransferObjects\ConsolidationEntriesData;
use Domain\ConsolidationEntries\Interfaces\ConsolidationEntriesRepositoryInterface;
use Domain\ConsolidationEntries\Models\ConsolidationEntries;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class ConsolidationEntriesRepository extends BaseRepository implements ConsolidationEntriesRepositoryInterface
{
    protected mixed $model = ConsolidationEntries::class;

    const DATE_COLUMN = 'date';
    const CONSOLIDATED_COLUMN = 'consolidated';

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
    public function getConsolidationEntriesByStatus(int $status): Collection
    {
        //$this->requiredRelationships = [];
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => self::CONSOLIDATED_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => $status,]
        ];

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
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
     * @return bool
     */
    public function deleteConsolidationEntry(string $date): bool
    {
        return $this->deleteByColumn(self::DATE_COLUMN, $date);
    }
}
