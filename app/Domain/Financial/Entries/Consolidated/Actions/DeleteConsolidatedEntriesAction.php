<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;
use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteConsolidatedEntriesAction
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;

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
