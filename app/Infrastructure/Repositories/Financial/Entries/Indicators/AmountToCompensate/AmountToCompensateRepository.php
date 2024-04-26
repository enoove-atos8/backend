<?php

namespace Infrastructure\Repositories\Financial\Entries\Indicators\AmountToCompensate;

use App\Domain\Financial\Entries\Consolidated\Models\ConsolidationEntries;
use App\Domain\Financial\Entries\General\Models\Entry;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Financial\Entries\Indicators\AmountToCompensate\Interfaces\AmountToCompensateRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class AmountToCompensateRepository extends BaseRepository implements AmountToCompensateRepositoryInterface
{
    protected mixed $model = Entry::class;
    const TABLE_NAME = 'entries';


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
    public function getEntriesAmountToCompensate(): Collection
    {
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => EntryRepository::DELETED_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => 0,
            ]
        ];

        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => null,
            ]
        ];

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }
}
