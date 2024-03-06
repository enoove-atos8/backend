<?php

namespace Domain\Entries\Consolidated\Actions;
use Domain\Entries\Consolidated\Constants\ReturnMessages;
use Domain\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;

class DeleteConsolidatedEntriesAction
{
    private ConsolidatedEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
    }


    /**
     * @param string $date
     * @return bool
     * @throws GeneralExceptions
     */
    public function __invoke(string $date): bool
    {
        $response = $this->consolidationEntriesRepository->deleteConsolidationEntry($date);

        if($response)
            return true;
        else
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRIES_CONSOLIDATED, 500);
    }

}
