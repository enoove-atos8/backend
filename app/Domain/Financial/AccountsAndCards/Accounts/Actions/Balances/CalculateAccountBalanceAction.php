<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Actions\Balances;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountBalanceData;
use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Carbon\Carbon;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountByIdAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class CalculateAccountBalanceAction
{
    private GetEntriesAction $getEntriesAction;

    private GetExitsAction $getExitsAction;

    private GetBalanceByAccountAndDateAction $getBalanceByAccountAndDateAction;

    private GetAccountByIdAction $getAccountByIdAction;

    public function __construct(
        GetEntriesAction $getEntriesAction,
        GetExitsAction $getExitsAction,
        GetBalanceByAccountAndDateAction $getBalanceByAccountAndDateAction,
        GetAccountByIdAction $getAccountByIdAction
    ) {
        $this->getEntriesAction = $getEntriesAction;
        $this->getExitsAction = $getExitsAction;
        $this->getBalanceByAccountAndDateAction = $getBalanceByAccountAndDateAction;
        $this->getAccountByIdAction = $getAccountByIdAction;
    }

    /**
     * Calculate account balance for a specific reference date
     * Formula: Current Month Balance = Previous Month Balance + Entries - Exits
     *
     * Note: Includes ALL entries and exits (including accounts_transfer type)
     * to reflect exactly what appears in the bank statement
     *
     * @param  string  $referenceDate  Format: Y-m
     *
     * @throws Throwable
     */
    public function execute(int $accountId, string $referenceDate): AccountBalanceData
    {
        // Buscar TODAS as entradas do mês (incluindo accounts_transfer)
        $entries = $this->getEntriesAction->execute($referenceDate, [], false)
            ->where(EntryRepository::ACCOUNT_ID_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], $accountId);

        $totalEntries = $entries->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);

        // Buscar TODAS as saídas do mês
        $exits = $this->getExitsAction->execute($referenceDate, [], false)
            ->where(ExitData::ACCOUNT_ID_PROPERTY, BaseRepository::OPERATORS['EQUALS'], $accountId);

        $totalExits = $exits->sum(ExitData::AMOUNT_PROPERTY);

        // Determinar saldo do mês anterior
        $previousMonthBalance = $this->calculatePreviousMonthBalance($accountId, $referenceDate);

        // Calcular saldo final do mês atual
        $currentMonthBalance = $previousMonthBalance + $totalEntries - $totalExits;

        // Verificar se é o primeiro registro de saldo
        $isInitialBalance = $this->isFirstBalanceRecord($accountId, $referenceDate);

        return new AccountBalanceData([
            'accountId' => $accountId,
            'referenceDate' => $referenceDate,
            'previousMonthBalance' => $previousMonthBalance,
            'currentMonthBalance' => $currentMonthBalance,
            'isInitialBalance' => $isInitialBalance,
        ]);
    }

    /**
     * Calculate previous month balance with priority order:
     *
     * Priority 1: Check if exists balance record in accounts_balances for previous month
     *            If exists, use its current_month_balance
     *
     * Priority 2: Check if previous month matches account's initial_balance_date
     *            If matches, use initial_balance (which represents last day of that month)
     *
     * Priority 3: Return 0 (no previous balance found)
     *
     * Example:
     * - Account registered on 05/11/2025
     * - initial_balance = 20000, initial_balance_date = "2025-10" (represents 31/10/2025)
     * - When processing Nov/2025: previousMonth = "2025-10", returns 20000
     * - When processing Dec/2025: previousMonth = "2025-11", returns Nov's current_month_balance
     *
     * @param  string  $referenceDate  Format: Y-m
     *
     * @throws Throwable
     */
    private function calculatePreviousMonthBalance(int $accountId, string $referenceDate): float
    {
        // Calcular o mês anterior ao mês de referência
        $previousMonth = Carbon::createFromFormat('Y-m', $referenceDate)->subMonth()->format('Y-m');

        // Prioridade 1: Buscar saldo registrado do mês anterior em accounts_balances
        $previousBalance = $this->getBalanceByAccountAndDateAction->execute($accountId, $previousMonth);

        if ($previousBalance) {
            // Usa o saldo final (current_month_balance) do mês anterior
            return $previousBalance->currentMonthBalance ?? 0;
        }

        // Prioridade 2: Verificar se o mês anterior coincide com initial_balance_date
        $account = $this->getAccountByIdAction->execute($accountId);

        if ($account && $account->initialBalance !== null && $account->initialBalanceDate) {
            // O initial_balance_date representa o último dia daquele mês
            if ($account->initialBalanceDate === $previousMonth) {
                return (float) $account->initialBalance;
            }
        }

        // Prioridade 3: Não existe saldo anterior
        return 0;
    }

    /**
     * Check if this is the first balance record for the account
     * Returns true if there's no balance record for the previous month
     *
     * @param  string  $referenceDate  Format: Y-m
     *
     * @throws Throwable
     */
    private function isFirstBalanceRecord(int $accountId, string $referenceDate): bool
    {
        $previousMonth = Carbon::createFromFormat('Y-m', $referenceDate)->subMonth()->format('Y-m');
        $previousBalance = $this->getBalanceByAccountAndDateAction->execute($accountId, $previousMonth);

        return $previousBalance === null;
    }
}
