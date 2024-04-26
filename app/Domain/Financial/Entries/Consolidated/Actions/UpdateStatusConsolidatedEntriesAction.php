<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;

use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;

class UpdateStatusConsolidatedEntriesAction
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;
    private EntryRepository $entryRepository;
    private UpdateAmountConsolidatedEntriesAction $updateAmountConsolidationEntriesAction;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface,
        EntryRepositoryInterface                $entryRepositoryInterface,
        UpdateAmountConsolidatedEntriesAction   $updateAmountConsolidationEntriesAction,
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
        $this->updateAmountConsolidationEntriesAction = $updateAmountConsolidationEntriesAction;
        $this->entryRepository = $entryRepositoryInterface;
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
        $arrCountedNotCompensateEntry = [];

        foreach ($dates as $date)
        {
            $countedNotCompensateEntry = $this->entryRepository->getAllEntriesByDateAndType($date, 'register')
                ->where(
                    EntryRepository::COMPENSATED_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    EntryRepository::TO_COMPENSATE_VALUE)
                ->count();

            if($countedNotCompensateEntry > 0)
                $arrCountedNotCompensateEntry[] = $date;

        }


        if(count($arrCountedNotCompensateEntry) < 1)
        {
            $response = $this->consolidationEntriesRepository->updateConsolidationStatus($dates, $status);

            if($status == '1')
            {
                foreach ($dates as $date)
                {
                    $this->updateAmountConsolidationEntriesAction->__invoke($date);
                }
            }

            if($response)
                return true;
            else
                throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRIES_CONSOLIDATED, 500);
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_NOT_COMPENSATED_ENTRIES_FOUNDED, 500);
        }

    }

}
