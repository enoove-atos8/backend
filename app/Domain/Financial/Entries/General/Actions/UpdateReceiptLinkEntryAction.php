<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\Consolidated\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class UpdateReceiptLinkEntryAction
{
    private EntryRepository $entryRepository;

    public function __construct(
        EntryRepositoryInterface      $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;

    }

    /**
     * @param int $entryId
     * @param string $receiptLink
     * @return bool|mixed
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke(int $entryId, string $receiptLink): mixed
    {
        $entry = $this->entryRepository->updateReceiptLink($entryId, $receiptLink);

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
