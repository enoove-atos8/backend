<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use App\Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountsBalancesRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Support\Collection;

class DeleteFutureProcessedDataAction
{
    private AccountFileRepositoryInterface $accountFilesRepository;

    private AccountMovementsRepositoryInterface $accountMovementsRepository;

    private EntryRepositoryInterface $entryRepository;

    private ExitRepositoryInterface $exitRepository;

    private AccountsBalancesRepositoryInterface $accountsBalancesRepository;

    public function __construct(
        AccountFileRepositoryInterface $accountFilesRepository,
        AccountMovementsRepositoryInterface $accountMovementsRepository,
        EntryRepositoryInterface $entryRepository,
        ExitRepositoryInterface $exitRepository,
        AccountsBalancesRepositoryInterface $accountsBalancesRepository
    ) {
        $this->accountFilesRepository = $accountFilesRepository;
        $this->accountMovementsRepository = $accountMovementsRepository;
        $this->entryRepository = $entryRepository;
        $this->exitRepository = $exitRepository;
        $this->accountsBalancesRepository = $accountsBalancesRepository;
    }

    /**
     * Delete all processed data for months after the given reference date
     *
     * This is used when a user wants to reprocess an older month.
     * All data from subsequent months must be deleted to maintain data integrity.
     *
     * @param  string  $referenceDate  Format: Y-m (the month being reprocessed)
     * @return Collection Collection of deleted months (for logging/display)
     */
    public function execute(int $accountId, string $referenceDate): Collection
    {
        // Get all processed files after the reference date
        $futureProcessedFiles = $this->accountFilesRepository->getProcessedFilesAfterDate($accountId, $referenceDate);

        if ($futureProcessedFiles->isEmpty()) {
            return collect();
        }

        $deletedMonths = collect();

        foreach ($futureProcessedFiles as $file) {
            $fileReferenceDate = $file->referenceDate;

            // 1. Delete account movements for this month
            $this->accountMovementsRepository->deleteByAccountAndReferenceDate($accountId, $fileReferenceDate);

            // 2. Delete anonymous entries for this month
            $this->entryRepository->deleteAnonymousEntriesByAccountAndDate($accountId, $fileReferenceDate);

            // 3. Delete anonymous exits for this month
            $this->exitRepository->deleteAnonymousExitsByAccountAndDate($accountId, $fileReferenceDate);

            // 4. Delete account balances for this month
            $this->accountsBalancesRepository->deleteByAccountAndReferenceDate($accountId, $fileReferenceDate);

            // 5. Reset file status to 'to_process'
            $this->accountFilesRepository->resetFileStatusToProcess($file->id);

            $deletedMonths->push($fileReferenceDate);
        }

        return $deletedMonths;
    }
}
