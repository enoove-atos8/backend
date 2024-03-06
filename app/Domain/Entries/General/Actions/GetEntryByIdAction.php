<?php

namespace Domain\Entries\General\Actions;

use Domain\Entries\General\Constants\ReturnMessages;
use Domain\Entries\General\Interfaces\EntryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\General\EntryRepository;
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
