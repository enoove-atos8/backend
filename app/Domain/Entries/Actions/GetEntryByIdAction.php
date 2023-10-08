<?php

namespace Domain\Entries\Actions;

use Domain\Entries\Interfaces\EntryRepositoryInterface;
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
    public function __invoke($id): Model | null
    {
        $entry = $this->entryRepository->getEntryById($id);

        if($entry == null)
            throw new GeneralExceptions('Nenhum entrada encontrada!', 404);

        return $entry;
    }
}
