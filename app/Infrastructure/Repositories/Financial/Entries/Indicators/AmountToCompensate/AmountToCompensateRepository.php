<?php

namespace Infrastructure\Repositories\Financial\Entries\Indicators\AmountToCompensate;

use App\Domain\Financial\Entries\Consolidation\Models\Consolidation;
use App\Domain\Financial\Entries\Entries\Models\Entry;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Entries\Indicators\AmountToCompensate\Interfaces\AmountToCompensateRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class AmountToCompensateRepository extends BaseRepository implements AmountToCompensateRepositoryInterface
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
    public function getEntriesAmountToCompensate(): Collection
    {
        $this->queryConditions = [];

        $this->queryConditions [] = $this->whereEqual(EntryRepository::DELETED_COLUMN, 0, 'and');
        $this->queryConditions [] = $this->whereEqual(EntryRepository::COMPENSATED_COLUMN_JOINED, EntryRepository::TO_COMPENSATE_VALUE, 'and');
        $this->queryConditions [] = $this->whereEqual(EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN, null, 'and');


        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }
}
