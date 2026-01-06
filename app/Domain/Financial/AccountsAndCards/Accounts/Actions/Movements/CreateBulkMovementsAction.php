<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use Carbon\Carbon;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountMovementsRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Throwable;

class CreateBulkMovementsAction
{
    // Tolerância para comparação de valores decimais
    private const AMOUNT_TOLERANCE = 0.01;

    private AccountMovementsRepositoryInterface $accountMovementsRepository;

    private CreateAnonymousOffersByMovementsAction $createAnonymousOffersByMovementsAction;

    private CreateAnonymousExitsByMovementsAction $createAnonymousExitsByMovementsAction;

    private ReconcileAccountMovementsAction $reconcileAccountMovementsAction;

    private GetExitsAction $getExitsAction;

    public function __construct(
        AccountMovementsRepositoryInterface $accountMovementsRepository,
        CreateAnonymousOffersByMovementsAction $createAnonymousOffersByMovementsAction,
        CreateAnonymousExitsByMovementsAction $createAnonymousExitsByMovementsAction,
        ReconcileAccountMovementsAction $reconcileAccountMovementsAction,
        GetExitsAction $getExitsAction
    ) {
        $this->accountMovementsRepository = $accountMovementsRepository;
        $this->createAnonymousOffersByMovementsAction = $createAnonymousOffersByMovementsAction;
        $this->createAnonymousExitsByMovementsAction = $createAnonymousExitsByMovementsAction;
        $this->reconcileAccountMovementsAction = $reconcileAccountMovementsAction;
        $this->getExitsAction = $getExitsAction;
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

            // Validar e corrigir status de conciliação de débitos sem exits correspondentes
            if ($referenceDate) {
                $this->validateAndFixConciliationStatus($accountId, $referenceDate);
            }

            // Criar ofertas e saídas anônimas se necessário
            if ($referenceDate) {
                $this->createAnonymousOffersByMovementsAction->execute($accountId, $referenceDate);
                $this->createAnonymousExitsByMovementsAction->execute($accountId, $referenceDate);
            }
        }

        return $result;
    }

    /**
     * Valida e corrige o status de conciliação dos débitos.
     *
     * Identifica débitos marcados como "conciliated" mas que não têm exits correspondentes
     * e os marca como "not_found".
     *
     * @param  int  $accountId
     * @param  string  $referenceDate  Formato: Y-m
     *
     * @throws Throwable
     */
    private function validateAndFixConciliationStatus(int $accountId, string $referenceDate): void
    {
        // 1. Buscar movimentações do mês
        $movements = $this->accountMovementsRepository->getMovements($accountId, $referenceDate, false);

        // Filtrar apenas débitos conciliados
        $conciliatedDebits = $movements->filter(function ($movement) {
            return $movement->movementType === AccountMovementsRepository::MOVEMENT_TYPE_DEBIT
                && $movement->conciliatedStatus === AccountMovementsRepository::STATUS_CONCILIATED;
        });

        if ($conciliatedDebits->isEmpty()) {
            return;
        }

        // 2. Buscar exits do mês
        $exits = $this->getExitsAction->execute($referenceDate, [AccountMovementsRepository::ACCOUNT_ID_COLUMN => $accountId], false);

        // Filtrar exits excluindo anonymous_exits
        $validExits = $exits->filter(function ($exit) {
            return $exit->exitType !== ExitRepository::ANONYMOUS_EXITS_VALUE;
        });

        // 3. Identificar débitos conciliados sem exit correspondente
        $debitsToFix = [];

        foreach ($conciliatedDebits as $debit) {
            $hasMatchingExit = $this->findMatchingExit($debit, $validExits);

            if (! $hasMatchingExit) {
                $debitsToFix[] = $debit->id;
            }
        }

        // 4. Atualizar status para "not_found"
        if (! empty($debitsToFix)) {
            $updateMap = [];
            foreach ($debitsToFix as $id) {
                $updateMap[$id] = AccountMovementsRepository::STATUS_MOVEMENT_NOT_FOUND;
            }

            $this->accountMovementsRepository->bulkUpdateConciliationStatus($updateMap);
        }
    }

    /**
     * Busca um exit correspondente ao débito.
     *
     * @param  object  $debit
     * @return bool
     */
    private function findMatchingExit(object $debit, Collection $validExits): bool
    {
        return $validExits->contains(function ($exit) use ($debit) {
            $exitDate = Carbon::parse($exit->dateTransactionCompensation)->format('Y-m-d');
            $debitDate = $debit->movementDate;
            $amountDiff = abs($exit->amount - $debit->amount);

            return $exitDate === $debitDate && $amountDiff < self::AMOUNT_TOLERANCE;
        });
    }
}
