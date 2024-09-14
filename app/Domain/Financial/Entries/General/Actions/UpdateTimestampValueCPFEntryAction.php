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

class UpdateTimestampValueCPFEntryAction
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
     * @param string $timestampValueCpf
     * @return bool|mixed
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke(int $entryId, string $timestampValueCpf): mixed
    {
        $entry = $this->entryRepository->updateTimestampValueCpf($entryId, $timestampValueCpf);

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
