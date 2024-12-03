<?php

namespace App\Domain\Financial\Entries\Consolidation\Actions;

use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateConsolidatedEntryAction
{
    private ConsolidationRepository $consolidationEntriesRepository;

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
