<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GetEntryByTimestampValueCpfAction
{
    private EntryRepositoryInterface $entryRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute($timestampValueCpf): Model | null
    {
        $entry = $this->entryRepository->getEntryByTimestampValueCpf($timestampValueCpf);

        if($entry != null)
        {
            return $entry;
        }
        else
        {
            return null;
        }
    }
}
