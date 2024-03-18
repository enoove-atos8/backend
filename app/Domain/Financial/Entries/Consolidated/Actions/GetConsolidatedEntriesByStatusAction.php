<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;

use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetConsolidatedEntriesByStatusAction
{
    private ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepository;
    private GetQtdEntriesNoCompensateByMonthAction $getQtdEntriesNoCompensateByMonthAction;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface,
        GetQtdEntriesNoCompensateByMonthAction  $getQtdEntriesNoCompensateByMonthAction
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
        $this->getQtdEntriesNoCompensateByMonthAction = $getQtdEntriesNoCompensateByMonthAction;
    }


    /**
     * @throws GeneralExceptions
     */
    public function __invoke(string $consolidated, bool $returnNumberEntriesNoCompensate = false): Collection
    {
        $consolidatedEntriesByStatus = $this->consolidationEntriesRepository->getConsolidatedEntriesByStatus($consolidated);

        if(count($consolidatedEntriesByStatus) > 0)
        {
            if($returnNumberEntriesNoCompensate)
            {
                $consolidatedEntriesByStatus->map(function ($value)
                {
                    $countEntriesNoCompensateByMonth = $this->getQtdEntriesNoCompensateByMonthAction->__invoke($value->date);
                    $value['entriesNoCompensate'] = $countEntriesNoCompensateByMonth;
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
            throw new GeneralExceptions(ReturnMessages::ERROR_GET_CONSOLIDATED_ENTRIES_NOT_FOUND, 404);
        }
    }
}
