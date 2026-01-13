<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;

class GetTithesByMemberIdsAction
{
    private EntryRepositoryInterface $entryRepository;

    public function __construct(EntryRepositoryInterface $entryRepositoryInterface)
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * Busca todas as entries de dízimos para uma lista de membros
     *
     * @throws Throwable
     */
    public function execute(array $memberIds): Collection
    {
        return $this->entryRepository->getTithesByMemberIds($memberIds);
    }
}
