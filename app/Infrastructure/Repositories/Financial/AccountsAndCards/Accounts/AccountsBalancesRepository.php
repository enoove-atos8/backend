<?php

namespace App\Infrastructure\Repositories\Financial\AccountsAndCards\Accounts;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountBalanceData;
use App\Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountsBalancesRepositoryInterface;
use App\Domain\Financial\AccountsAndCards\Accounts\Models\AccountsBalances;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\BaseRepository;

class AccountsBalancesRepository extends BaseRepository implements AccountsBalancesRepositoryInterface
{
    protected mixed $model = AccountsBalances::class;

    const TABLE_NAME = 'accounts_balances';

    const ID_COLUMN = 'id';

    const ACCOUNT_ID_COLUMN = 'account_id';

    const REFERENCE_DATE_COLUMN = 'reference_date';

    /**
     * @throws BindingResolutionException
     */
    public function getBalanceByAccountAndDate(int $accountId, string $referenceDate): ?AccountBalanceData
    {
        $query = function () use ($accountId, $referenceDate) {
            $result = $this->model::where(self::ACCOUNT_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $accountId)
                ->where(self::REFERENCE_DATE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $referenceDate)
                ->first();

            if (! $result) {
                return null;
            }

            return AccountBalanceData::fromResponse($result->toArray());
        };

        return $this->doQuery($query);
    }

    /**
     * @throws BindingResolutionException
     */
    public function saveOrUpdateBalance(AccountBalanceData $accountBalanceData): AccountBalanceData
    {
        $query = function () use ($accountBalanceData) {
            $existing = $this->model::where(self::ACCOUNT_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $accountBalanceData->accountId)
                ->where(self::REFERENCE_DATE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $accountBalanceData->referenceDate)
                ->first();

            if ($existing) {
                // Update
                $existing->update([
                    'previous_month_balance' => $accountBalanceData->previousMonthBalance,
                    'current_month_balance' => $accountBalanceData->currentMonthBalance,
                ]);

                return AccountBalanceData::fromResponse($existing->fresh()->toArray());
            } else {
                // Insert
                $created = $this->model::create([
                    'account_id' => $accountBalanceData->accountId,
                    'reference_date' => $accountBalanceData->referenceDate,
                    'previous_month_balance' => $accountBalanceData->previousMonthBalance,
                    'current_month_balance' => $accountBalanceData->currentMonthBalance,
                    'is_initial_balance' => $accountBalanceData->isInitialBalance ?? false,
                ]);

                return AccountBalanceData::fromResponse($created->toArray());
            }
        };

        return $this->doQuery($query);
    }
}
