<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Illuminate\Support\Collection;

class CreateBulkMovementsAction
{
    private AccountMovementsRepositoryInterface $accountMovementsRepository;

    public function __construct(AccountMovementsRepositoryInterface $accountMovementsRepository)
    {
        $this->accountMovementsRepository = $accountMovementsRepository;
    }

    /**
     * Execute bulk creation of account movements
     *
     * @param Collection $movements
     * @param int $accountId
     * @param int $fileId
     * @return bool
     */
    public function execute(Collection $movements, int $accountId, int $fileId): bool
    {
        return $this->accountMovementsRepository->bulkCreateMovements($movements, $accountId, $fileId);
    }
}
