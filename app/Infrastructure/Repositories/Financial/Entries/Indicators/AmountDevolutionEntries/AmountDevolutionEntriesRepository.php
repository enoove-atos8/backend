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
     * Array of conditions
     */
    private array $queryConditions = [];

    /**
     * @throws BindingResolutionException
     */
    public function getDevolutionEntriesAmount(): Collection
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(EntryRepository::DELETED_COLUMN, 0, 'and');
        $this->queryConditions [] = $this->whereEqual(EntryRepository::DEVOLUTION_COLUMN, 1, 'and');


        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }
}
