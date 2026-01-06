<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;

class CreateBulkMovementsAction
{
    private AccountMovementsRepositoryInterface $accountMovementsRepository;

    private CreateAnonymousOffersByMovementsAction $createAnonymousOffersByMovementsAction;

    private CreateAnonymousExitsByMovementsAction $createAnonymousExitsByMovementsAction;

    private ReconcileAccountMovementsAction $reconcileAccountMovementsAction;

    public function __construct(
        AccountMovementsRepositoryInterface $accountMovementsRepository,
        CreateAnonymousOffersByMovementsAction $createAnonymousOffersByMovementsAction,
        CreateAnonymousExitsByMovementsAction $createAnonymousExitsByMovementsAction,
        ReconcileAccountMovementsAction $reconcileAccountMovementsAction
    ) {
        $this->accountMovementsRepository = $accountMovementsRepository;
        $this->createAnonymousOffersByMovementsAction = $createAnonymousOffersByMovementsAction;
        $this->createAnonymousExitsByMovementsAction = $createAnonymousExitsByMovementsAction;
        $this->reconcileAccountMovementsAction = $reconcileAccountMovementsAction;
    }

    /**
     * Execute bulk creation of account movements
     *
     * @param  string|null  $referenceDate  Date in format YYYY-MM for anonymous offers calculation
     *
     * @throws Throwable
     */
    public function execute(Collection $movements, int $accountId, int $fileId, ?string $referenceDate = null): bool
    {
        $result = $this->accountMovementsRepository->bulkCreateMovements($movements, $accountId, $fileId);

        if ($result) {
            // Realizar conciliação bancária após inserção dos movimentos
            $this->reconcileAccountMovementsAction->execute($accountId, $fileId);

            // Criar ofertas e saídas anônimas se necessário
            if ($referenceDate) {
                $this->createAnonymousOffersByMovementsAction->execute($accountId, $referenceDate);
                $this->createAnonymousExitsByMovementsAction->execute($accountId, $referenceDate);
            }
        }

        return $result;
    }
}
