<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;

class DeleteAnonymousEntriesAndExitsAction
{
    private EntryRepositoryInterface $entryRepository;

    private ExitRepositoryInterface $exitRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepository,
        ExitRepositoryInterface $exitRepository
    ) {
        $this->entryRepository = $entryRepository;
        $this->exitRepository = $exitRepository;
    }

    /**
     * Delete anonymous entries and exits by account and reference date
     * This is used during reprocessing to remove anonymous transactions
     * that might now be identified in the new extract
     *
     * @param  string  $referenceDate  Format: Y-m
     */
    public function execute(int $accountId, string $referenceDate): void
    {
        $this->entryRepository->deleteAnonymousEntriesByAccountAndDate($accountId, $referenceDate);
        $this->exitRepository->deleteAnonymousExitsByAccountAndDate($accountId, $referenceDate);
    }
}
