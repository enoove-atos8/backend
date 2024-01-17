<?php

namespace Domain\ConsolidationEntries\Actions;
use Domain\ConsolidationEntries\Constants\ReturnMessages;
use Domain\ConsolidationEntries\Interfaces\ConsolidationEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\ConsolidationEntries\ConsolidationEntriesRepository;

class UpdateStatusConsolidationEntriesAction
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidationEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
    }


    /**
     * @param array $dates
     * @param string $status
     * @return bool
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke(array $dates, string $status): bool
    {
        $response = $this->consolidationEntriesRepository->updateConsolidationStatus($dates, $status);

        if($response)
            return true;
        else
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRIES_CONSOLIDATED, 500);
    }

}
