<?php

namespace Domain\ConsolidationEntries\Actions;

use Domain\ConsolidationEntries\Constants\ReturnMessages;
use Domain\ConsolidationEntries\DataTransferObjects\ConsolidationEntriesData;
use Domain\ConsolidationEntries\Interfaces\ConsolidationEntriesRepositoryInterface;
use Domain\ConsolidationEntries\Models\ConsolidationEntries;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\ConsolidationEntries\ConsolidationEntriesRepository;

class CreateConsolidatedEntryAction
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidationEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
    }


    /**
     * @param ConsolidationEntriesData $consolidationEntriesData
     * @return void
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke(ConsolidationEntriesData $consolidationEntriesData): void
    {
        if($consolidationEntriesData->date !== null)
        {
            $existConsolidationRegister = $this->consolidationEntriesRepository->getByDate(substr($consolidationEntriesData->date, 0, 7));

            if($existConsolidationRegister == null)
                $this->consolidationEntriesRepository->new($consolidationEntriesData);

            if($existConsolidationRegister !== null and $existConsolidationRegister->consolidated == 1)
                throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_ENTRIES_CONSOLIDATED_MONTH, 500);
        }
    }
}
