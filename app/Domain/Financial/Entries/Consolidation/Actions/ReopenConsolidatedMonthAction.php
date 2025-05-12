<?php

namespace Domain\Financial\Entries\Consolidation\Actions;

use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;

class ReopenConsolidatedMonthAction
{
    private ConsolidatedEntriesRepositoryInterface $consolidationRepository;

    public function __construct(ConsolidatedEntriesRepositoryInterface $consolidationRepository)
    {
        $this->consolidationRepository = $consolidationRepository;
    }

    /**
     * @throws GeneralExceptions
     */
    public function execute(string $month): bool
    {
        $reopened = $this->consolidationRepository->reopenConsolidatedMonth($month);

        if($reopened)
            return true;

        else
            throw new GeneralExceptions(ReturnMessages::REOPEN_CONSOLIDATED_MONTH_ERROR, 500);
    }
}
