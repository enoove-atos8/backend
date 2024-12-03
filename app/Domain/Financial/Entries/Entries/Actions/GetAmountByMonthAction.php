<?php

namespace Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;

class GetAmountByMonthAction
{
    private EntryRepositoryInterface $entryRepositoryInterface;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface
    )
    {
        $this->entryRepositoryInterface = $entryRepositoryInterface;
    }


    public function __invoke(string $date): float
    {
        $entries = $this->entryRepositoryInterface->getAllEntriesByDateAndType($date, EntryRepository::REGISTER_INDICATOR);
        return $entries->sum(EntryRepository::AMOUNT_COLUMN);
    }
}
