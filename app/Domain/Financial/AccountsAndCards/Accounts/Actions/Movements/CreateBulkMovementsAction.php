<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;

class CreateBulkMovementsAction
{
    private AccountMovementsRepositoryInterface $accountMovementsRepository;
    private CreateAnonymousOffersByMovements $createAnonymousOffersByMovements;

    public function __construct(
        AccountMovementsRepositoryInterface $accountMovementsRepository,
        CreateAnonymousOffersByMovements $createAnonymousOffersByMovements
    )
    {
        $this->accountMovementsRepository = $accountMovementsRepository;
        $this->createAnonymousOffersByMovements = $createAnonymousOffersByMovements;
    }

    /**
     * Execute bulk creation of account movements
     *
     * @param Collection $movements
     * @param int $accountId
     * @param int $fileId
     * @param string|null $referenceDate Date in format YYYY-MM for anonymous offers calculation
     * @return bool
     * @throws Throwable
     */
    public function execute(Collection $movements, int $accountId, int $fileId, ?string $referenceDate = null): bool
    {
        $result = $this->accountMovementsRepository->bulkCreateMovements($movements, $accountId, $fileId);

        // Create anonymous offers entry after bulk insertion if reference date is provided
        if ($result && $referenceDate) {
            $this->createAnonymousOffersByMovements->execute($accountId, $referenceDate);
        }

        return $result;
    }
}
