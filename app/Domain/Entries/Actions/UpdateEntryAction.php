<?php

namespace Domain\Entries\Actions;

use App\Domain\Entries\Constants\ReturnMessages;
use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\EntryRepository;
use Throwable;

class UpdateEntryAction
{
    private EntryRepository $entryRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @param $id
     * @param EntryData $entryData
     * @return bool|mixed
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function __invoke($id, EntryData $entryData): mixed
    {
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
