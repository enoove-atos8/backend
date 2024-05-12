<?php

namespace App\Infrastructure\Repositories\Financial\Entries\Indicators\AmountDevolutionEntries;

use App\Domain\Financial\Entries\General\Models\Entry;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Financial\Entries\Indicators\AmountDevolutions\Interfaces\AmountDevolutionRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class AmountDevolutionEntriesRepository extends BaseRepository implements AmountDevolutionRepositoryInterface
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
    public function getDevolutionEntriesAmount(): Collection
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
            'condition' => ['field' => EntryRepository::DEVOLUTION_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => 1,
            ]
        ];


        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }
}
