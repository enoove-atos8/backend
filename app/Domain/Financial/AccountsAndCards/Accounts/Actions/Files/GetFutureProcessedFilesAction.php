<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Illuminate\Support\Collection;

class GetFutureProcessedFilesAction
{
    private AccountFileRepositoryInterface $accountFilesRepository;

    public function __construct(
        AccountFileRepositoryInterface $accountFilesRepository
    ) {
        $this->accountFilesRepository = $accountFilesRepository;
    }

    /**
     * Get all processed files after a given reference date for an account
     *
     * @param  string  $referenceDate  Format: Y-m
     * @return Collection Collection of AccountFileData
     */
    public function execute(int $accountId, string $referenceDate): Collection
    {
        return $this->accountFilesRepository->getProcessedFilesAfterDate($accountId, $referenceDate);
    }
}
