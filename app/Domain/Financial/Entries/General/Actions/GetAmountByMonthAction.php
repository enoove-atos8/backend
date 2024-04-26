<?php

namespace Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;

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
