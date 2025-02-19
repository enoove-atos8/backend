<?php

namespace App\Domain\Financial\Entries\Consolidation\Actions;

use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Entries\Entries\Actions\GetAmountByMonthAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetConsolidatedEntriesByStatusAction
{
    private ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepository;
    private GetQtdEntriesNoCompensateByMonthAction $getQtdEntriesNoCompensateByMonthAction;
    private GetAmountByMonthAction $getAmountByMonthAction;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface,
        GetQtdEntriesNoCompensateByMonthAction  $getQtdEntriesNoCompensateByMonthAction,
        GetAmountByMonthAction  $getAmountByMonthAction,
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
        $this->getQtdEntriesNoCompensateByMonthAction = $getQtdEntriesNoCompensateByMonthAction;
        $this->getAmountByMonthAction = $getAmountByMonthAction;
    }


    /**
     * @param string|int $consolidated
     * @param bool $returnNumberEntriesNoCompensate
     * @param int $limit
     * @param bool $callableByTithesBalance
     * @return Collection
     * @throws GeneralExceptions
     */
    public function execute(string | int $consolidated, bool $returnNumberEntriesNoCompensate = false, int $limit = 6, bool $callableByTithesBalance = false): Collection
    {
        $consolidatedEntriesByStatus = $this->consolidationEntriesRepository->getConsolidatedMonths($consolidated);

        if(!$callableByTithesBalance)
        {
            if($returnNumberEntriesNoCompensate)
            {
                $consolidatedEntriesByStatus->map(function ($value) use($consolidatedEntriesByStatus)
                {
                    $entriesNoCompensate = $this->getQtdEntriesNoCompensateByMonthAction->execute($value->date);
                    $amountEntriesNoCompensate = $entriesNoCompensate->sum(EntryRepository::AMOUNT_COLUMN);
                    $value['entriesNoCompensate'] = $entriesNoCompensate->count();
                    $value['amountEntriesNoCompensate'] = $amountEntriesNoCompensate;
                    $value['amountEntries'] = $this->getAmountByMonthAction->execute($value->date);
                    return $value;
                });

                return $consolidatedEntriesByStatus;
            }
            else
            {
                return $consolidatedEntriesByStatus;
            }
        }
        else
        {
            if(count($consolidatedEntriesByStatus) > 0)
            {
                return $consolidatedEntriesByStatus;
            }
            else
            {
                throw new GeneralExceptions(ReturnMessages::ERROR_GET_CONSOLIDATED_ENTRIES, 404);
            }
        }

    }
}
