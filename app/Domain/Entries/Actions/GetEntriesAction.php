<?php

namespace Domain\Entries\Actions;

use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\EntryRepository;
use Throwable;

class GetEntriesAction
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
    public function __invoke($request): Collection
    {
        $range = $request->input('dates');
        $entries = $this->entryRepository->getAllEntries($range);

        if($entries->count() == 0)
            throw new GeneralExceptions('Não foram encontradas entradas para este mês ou os filtros aplicados não retornaram resultados...', 404);

        return $entries;
    }
}
