<?php

namespace Domain\ConsolidationEntries\Actions;

use Domain\ConsolidationEntries\Constants\ReturnMessages;
use Domain\ConsolidationEntries\Interfaces\ConsolidationEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\ConsolidationEntries\ConsolidationEntriesRepository;

class GetConsolidationEntriesByStatus
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidationEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function __invoke(int $status): Collection
    {
        $consolidatedEntriesByStatus = $this->consolidationEntriesRepository->getConsolidationEntriesByStatus($status);

        if(count($consolidatedEntriesByStatus) > 0)
        {
            return $consolidatedEntriesByStatus;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_GET_CONSOLIDATED_ENTRIES_NOT_FOUND, 404);
        }
    }
}
