<?php

namespace Domain\Entries\Actions;

use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\EntryRepository;

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
     * @throws \Throwable
     */
    public function __invoke($id, EntryData $entryData): bool
    {
        $entry = $this->entryRepository->updateEntry($id, $entryData);

        if($entry)
            return true;
        else
            throw new GeneralExceptions('Encontramos um problema ao atualizar esta entrada, tente mais tarde!', 500);
    }
}
