<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Movements\GetMovementsAction;
use Carbon\Carbon;
use Domain\Financial\Exits\Exits\Actions\CreateExitAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Domain\Financial\Reviewers\Actions\GetReviewerAction;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\MovementsRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Throwable;

class CreateAnonymousExitsByMovementsAction
{
    private GetMovementsAction $getMovementsAction;

    private GetExitsAction $getExitsAction;

    private CreateExitAction $createExitAction;

    private GetReviewerAction $getReviewerAction;

    private ExitRepositoryInterface $exitRepository;

    public function __construct(
        GetMovementsAction $getMovementsAction,
        GetExitsAction $getExitsAction,
        CreateExitAction $createExitAction,
        GetReviewerAction $getReviewerAction,
        ExitRepositoryInterface $exitRepository
    ) {
        $this->getMovementsAction = $getMovementsAction;
        $this->getExitsAction = $getExitsAction;
        $this->createExitAction = $createExitAction;
        $this->getReviewerAction = $getReviewerAction;
        $this->exitRepository = $exitRepository;
    }

    /**
     * Creates or updates an anonymous exit based on movements and registered exits.
     *
     * This handles bank fees, IOF, and other unregistered debits from bank statements.
     *
     * @param  string  $referenceDate  Date in format YYYY-MM
     * @return float|null Returns the amount created/updated, or null if no exit was created
     *
     * @throws Throwable
     */
    public function execute(int $accountId, string $referenceDate): ?float
    {
        $movements = $this->getMovementsAction->execute($accountId, $referenceDate, false);

        // Total de débitos no extrato bancário
        $totalExitsInBankExtract = $movements
            ->where(MovementsRepository::MOVEMENT_TYPE_COLUMN, MovementsRepository::DEBIT_VALUE)
            ->sum(MovementsRepository::AMOUNT_COLUMN);

        $exits = $this->getExitsAction->execute($referenceDate, [], false)
            ->where(ExitData::ACCOUNT_ID_PROPERTY, BaseRepository::OPERATORS['EQUALS'], $accountId);

        // Total de saídas registradas (excluindo accounts_transfer e saídas anônimas)
        $totalExits = $exits
            ->where(ExitRepository::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], false)
            ->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['NOT_EQUALS'], ExitRepository::ACCOUNTS_TRANSFER_VALUE)
            ->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['NOT_EQUALS'], ExitRepository::ANONYMOUS_EXITS_VALUE)
            ->sum(ExitData::AMOUNT_PROPERTY);

        // Total de transferências entre contas ENVIADAS desta conta
        // Buscar nas SAÍDAS (exits) as transferências onde account_id = esta conta
        $totalTransfersBetweenAccountsSent = $exits
            ->where(ExitRepository::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], false)
            ->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::ACCOUNTS_TRANSFER_VALUE)
            ->sum(ExitData::AMOUNT_PROPERTY);

        // Cálculo: (Débitos no extrato - Transferências enviadas) - Saídas registradas
        $anonymousExitsAmount = ($totalExitsInBankExtract - $totalTransfersBetweenAccountsSent) - $totalExits;

        $existingAnonymousExit = $this->getExistingAnonymousExit($accountId, $referenceDate);

        if ($anonymousExitsAmount > 0) {
            if ($existingAnonymousExit) {
                return $this->updateAnonymousExit($existingAnonymousExit->exits_id, $anonymousExitsAmount);
            } else {
                return $this->createAnonymousExit($accountId, $referenceDate, $anonymousExitsAmount);
            }
        }

        return null;
    }

    /**
     * Gets existing anonymous exit for account and period.
     *
     * @param  string  $referenceDate  Date in format YYYY-MM
     *
     * @throws Throwable
     */
    private function getExistingAnonymousExit(int $accountId, string $referenceDate): ?object
    {
        $exits = $this->getExitsAction->execute($referenceDate, [], false)
            ->where(ExitRepository::ACCOUNT_ID_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], $accountId)
            ->where(ExitRepository::EXIT_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], ExitRepository::ANONYMOUS_EXITS_VALUE)
            ->where(ExitRepository::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], false);

        return $exits->first();
    }

    /**
     * Updates an existing anonymous exit.
     *
     * @throws Throwable
     */
    private function updateAnonymousExit(int $exitId, float $amount): float
    {
        $reviewer = $this->getReviewerAction->execute();
        $existingExit = $this->exitRepository->getExitById($exitId);

        $exitData = new ExitData([
            'id' => $exitId,
            'amount' => $amount,
            'comments' => 'Saídas anônimas (tarifas/IOF/etc) geradas automaticamente após importação de movimentações',
            'dateExitRegister' => $existingExit->date_exit_register,
            'dateTransactionCompensation' => $existingExit->date_transaction_compensation,
            'deleted' => 0,
            'exitType' => ExitRepository::ANONYMOUS_EXITS_VALUE,
            'accountId' => $existingExit->account_id,
            'receiptLink' => null,
            'financialReviewerId' => $reviewer->id,
            'transactionCompensation' => ExitRepository::COMPENSATED_VALUE,
            'transactionType' => ExitRepository::PIX_VALUE,
            'divisionId' => null,
            'groupId' => null,
            'paymentCategoryId' => null,
            'paymentItemId' => null,
            'isPayment' => 0,
            'timestampExitTransaction' => null,
        ]);

        $this->exitRepository->updateExit($exitId, $exitData);

        return $amount;
    }

    /**
     * Creates an anonymous exit.
     *
     * @throws Throwable
     */
    private function createAnonymousExit(int $accountId, string $referenceDate, float $amount): float
    {
        $reviewer = $this->getReviewerAction->execute();

        [$year, $month] = explode('-', $referenceDate);
        $lastDayOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

        $exitData = new ExitData([
            'id' => null,
            'amount' => $amount,
            'comments' => 'Saídas anônimas (tarifas/IOF/etc) geradas automaticamente após importação de movimentações',
            'dateExitRegister' => $lastDayOfMonth,
            'dateTransactionCompensation' => $lastDayOfMonth.'T03:00:00.000Z',
            'deleted' => 0,
            'exitType' => ExitRepository::ANONYMOUS_EXITS_VALUE,
            'accountId' => $accountId,
            'receiptLink' => null,
            'financialReviewerId' => $reviewer->id,
            'transactionCompensation' => ExitRepository::COMPENSATED_VALUE,
            'transactionType' => ExitRepository::PIX_VALUE,
            'divisionId' => null,
            'groupId' => null,
            'paymentCategoryId' => null,
            'paymentItemId' => null,
            'isPayment' => 0,
            'timestampExitTransaction' => null,
        ]);

        $this->createExitAction->execute($exitData);

        return $amount;
    }
}
