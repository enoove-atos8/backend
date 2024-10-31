<?php

namespace Infrastructure\Repositories\Financial\Entries\Indicators\TotalGeneral;

use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\General\Models\Entry;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Financial\Entries\Indicators\TotalGeneral\Interfaces\TotalGeneralRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class TotalGeneralRepository extends BaseRepository implements TotalGeneralRepositoryInterface
{
    protected mixed $model = Entry::class;
    private EntryRepository $entryRepository;


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param EntryRepositoryInterface $entryRepositoryInterface
     * @throws BindingResolutionException
     */
    public function __construct(EntryRepositoryInterface $entryRepositoryInterface)
    {
        parent::__construct();
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @param string|null $rangeMonthlyDate
     * @param array $filters
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getTotalGeneralEntries(string|null $rangeMonthlyDate, array $filters): Collection
    {
        $arrRangeMonthlyDate = [];
        $this->requiredRelationships = [];

        if ($rangeMonthlyDate !== 'all' && $filters == null)
            $arrRangeMonthlyDate = explode(',', $rangeMonthlyDate);

        if ($rangeMonthlyDate == null && $filters != null)
        {
            if($filters['customDates'] != null)
                $arrRangeMonthlyDate = explode(',', $filters['customDates']);
        }

        $this->queryConditions[] = $this->whereEqual(EntryRepository::DELETED_COLUMN_JOINED, false, 'and');


        if ($rangeMonthlyDate !== 'all' && $filters == null)
        {
            $this->queryConditions[] = $this->whereLike(EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, $arrRangeMonthlyDate, 'andWithOrInside');
            $this->queryConditions[] = $this->whereEqual(EntryRepository::COMPENSATED_COLUMN_JOINED, EntryRepository::COMPENSATED_VALUE, 'and');
        }

        if ($rangeMonthlyDate !== 'all' && $filters != null)
        {
            if(!array_key_exists('customDates', $filters))
            {
                $this->queryConditions[] = $this->whereLike(EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, $arrRangeMonthlyDate, 'andWithOrInside');
                $this->queryConditions[] = $this->whereEqual(EntryRepository::COMPENSATED_COLUMN_JOINED, EntryRepository::COMPENSATED_VALUE, 'and');
            }
        }

        if ($rangeMonthlyDate == 'all')
            $this->queryConditions[] = $this->whereLike(EntryRepository::COMPENSATED_COLUMN_JOINED, EntryRepository::COMPENSATED_VALUE, 'and');

        if (count($filters) > 0)
        {
            $queryConditions = $this->entryRepository->applyFilters($filters, true, true);

            foreach ($queryConditions as $conditions)
            {
                $this->queryConditions[] = $conditions;
            }
        }

        return $this->entryRepository->qbGetEntriesWithMembersAndReviewers(
            $this->queryConditions,
            EntryRepository::DISPLAY_SELECT_COLUMNS,
            [EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, EntryRepository::ID_COLUMN_JOINED],
            false
        );

    }
}
