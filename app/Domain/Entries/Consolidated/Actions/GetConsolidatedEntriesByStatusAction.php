<?php

namespace Domain\Entries\Consolidated\Actions;

use Domain\Entries\Consolidated\Constants\ReturnMessages;
use Domain\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;

class GetConsolidatedEntriesByStatusAction
{
    private ConsolidatedEntriesRepository $consolidationEntriesRepository;
    private GetQtdEntriesNoCompensateByMonthAction $getQtdEntriesNoCompensateByMonthAction;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface,
        GetQtdEntriesNoCompensateByMonthAction $getQtdEntriesNoCompensateByMonthAction
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
        $this->getQtdEntriesNoCompensateByMonthAction = $getQtdEntriesNoCompensateByMonthAction;
    }


    /**
     * @throws GeneralExceptions|BindingResolutionException
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
