<?php

namespace Domain\Financial\Entries\DuplicitiesAnalisys\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

class GetReceiptsByEntriesIdsAction
{
    private EntryRepositoryInterface $entryRepository;


    public function __construct(EntryRepositoryInterface $entryRepositoryInterface)
    {
        $this->entryRepository = $entryRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     */
    public function execute(array $ids): Collection
    {
        return $this->entryRepository->getReceiptsEntriesByIds($ids);
    }
}
