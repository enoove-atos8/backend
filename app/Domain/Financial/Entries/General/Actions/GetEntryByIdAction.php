<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
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
