<?php

namespace Domain\Entries\Consolidated\Actions;

use Domain\Entries\Consolidated\Constants\ReturnMessages;
use Domain\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use Domain\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;
use Infrastructure\Repositories\Entries\General\EntryRepository;

class CreateConsolidatedEntryAction
{
    private ConsolidatedEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
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
            {
                $this->consolidationEntriesRepository->new($consolidationEntriesData);
            }

            if($existConsolidationRegister !== null and $existConsolidationRegister->consolidated == 1)
                throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_ENTRIES_CONSOLIDATED_MONTH, 500);
        }
    }
}
