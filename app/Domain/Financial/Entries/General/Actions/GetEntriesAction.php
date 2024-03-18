<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Members\Actions\GetMembersAction;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GetEntriesAction
{
    private EntryRepository $entryRepository;
    private GetMembersAction $getMembersAction;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        GetMembersAction $getMembersAction
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->getMembersAction = $getMembersAction;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $dates): Collection
    {
        $entries = $this->entryRepository->getAllEntries($dates);
        //$members = $this->getMembersAction->__invoke();

        if($entries->count() > 0)
        {
            return $entries;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_ENTRIES_FOUNDED, 404);
        }
    }
}
