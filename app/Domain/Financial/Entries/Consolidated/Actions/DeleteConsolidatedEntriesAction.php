<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;
use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\Interfaces\MonthlyTargetEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\MonthlyTargetEntriesRepository;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteConsolidatedEntriesAction
{
    private MonthlyTargetEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        MonthlyTargetEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
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
