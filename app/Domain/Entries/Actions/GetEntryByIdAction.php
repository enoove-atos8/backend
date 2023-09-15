<?php

namespace Domain\Entries\Actions;

use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
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
        return $this->entryRepository->getEntryById($id);
    }
}
