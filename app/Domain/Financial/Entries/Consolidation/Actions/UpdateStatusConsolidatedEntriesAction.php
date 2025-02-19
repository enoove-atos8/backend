<?php

namespace App\Domain\Financial\Entries\Consolidation\Actions;

use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;

class UpdateStatusConsolidatedEntriesAction
{
    private ConsolidationRepository $consolidationEntriesRepository;
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
    public function execute(string $date, string $status): bool
    {
        $response = $this->consolidationEntriesRepository->updateConsolidationStatus($date, $status);

        if($response)
        {
            return true;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRIES_CONSOLIDATED, 500);
        }

    }

}
