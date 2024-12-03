<?php

namespace App\Domain\Financial\Entries\Consolidation\Actions;
use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteConsolidatedEntriesAction
{
    private ConsolidationRepository $consolidationEntriesRepository;

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
