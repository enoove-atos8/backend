<?php

namespace Domain\Entries\Actions;

use Domain\Entries\Constants\ReturnMessages;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\EntryRepository;
use Throwable;

class GetEntryByIdAction
{
    private EntryRepository $entryRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function __invoke($id): Model
    {
        $entry = $this->entryRepository->getEntryById($id);

        if($entry !== null)
        {
            return $entry;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_ENTRY_FOUNDED, 404);
        }
    }
}
