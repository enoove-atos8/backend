<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Consolidation\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class UpdateIdentificationPendingEntryAction
{
    private EntryRepositoryInterface $entryRepository;

    public function __construct(
        EntryRepositoryInterface      $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;

    }

    /**
     * @param int $entryId
     * @param int $identificationPending
     * @return bool|mixed
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function execute(int $entryId, int $identificationPending): mixed
    {
        $entry = $this->entryRepository->updateIdentificationPending($entryId, $identificationPending);

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
