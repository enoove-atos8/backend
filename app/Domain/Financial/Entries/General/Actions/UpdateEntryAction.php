<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\MonthlyTargetEntriesData;
use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class UpdateEntryAction
{
    private EntryRepository $entryRepository;
    private \App\Domain\Financial\Entries\Consolidated\Actions\CreateConsolidatedEntryAction $createConsolidatedEntryAction;

    public function __construct(
        EntryRepositoryInterface                                                         $entryRepositoryInterface,
        \App\Domain\Financial\Entries\Consolidated\Actions\CreateConsolidatedEntryAction $createConsolidatedEntryAction,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;

    }

    /**
     * @param $id
     * @param EntryData $entryData
     * @param \App\Domain\Financial\Entries\Consolidated\DataTransferObjects\MonthlyTargetEntriesData $consolidationEntriesData
     * @return bool|mixed
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function __invoke($id, EntryData $entryData, MonthlyTargetEntriesData $consolidationEntriesData): mixed
    {
        $this->createConsolidatedEntryAction->__invoke($consolidationEntriesData);
        $entry = $this->entryRepository->updateEntry($id, $entryData);

        if($entry)
        {
            return $entry;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRY, 500);
        }
    }
}
