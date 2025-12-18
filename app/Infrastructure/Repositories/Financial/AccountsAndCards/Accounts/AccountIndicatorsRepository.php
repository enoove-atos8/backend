<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Accounts;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators\AccountIndicatorData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators\AccountRecentMovementsData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators\ConciliationStatusData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators\MonthSummaryData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators\PendingFileData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators\RecentMovementData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountIndicatorsRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Models\Accounts;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class AccountIndicatorsRepository extends BaseRepository implements AccountIndicatorsRepositoryInterface
{
    protected mixed $model = Accounts::class;

    const TABLE_NAME = 'accounts';

    const TABLE_ALIAS = 'a';

    const ID_COLUMN = 'id';

    const BANK_NAME_COLUMN = 'bank_name';

    const ACCOUNT_TYPE_COLUMN = 'account_type';

    const INITIAL_BALANCE_COLUMN = 'initial_balance';

    const INITIAL_BALANCE_DATE_COLUMN = 'initial_balance_date';

    const ACTIVATED_COLUMN = 'activated';

    const ID_COLUMN_ALIASED = 'a.id';

    const BANK_NAME_COLUMN_ALIASED = 'a.bank_name';

    const ACCOUNT_TYPE_COLUMN_ALIASED = 'a.account_type';

    const INITIAL_BALANCE_COLUMN_ALIASED = 'a.initial_balance';

    const INITIAL_BALANCE_DATE_COLUMN_ALIASED = 'a.initial_balance_date';

    const ACTIVATED_COLUMN_ALIASED = 'a.activated';

    const DISPLAY_SELECT_ACCOUNTS_INDICATORS = [
        'a.id as account_id',
        'a.bank_name',
        'a.account_type',
        'a.initial_balance',
        'a.initial_balance_date',
    ];

    const DISPLAY_SELECT_LAST_MOVEMENT_DATE = [
        '(SELECT MAX(movement_date) FROM accounts_movements WHERE account_id = a.id) as last_movement_date',
    ];

    const DISPLAY_SELECT_MOVEMENTS_BALANCE = [
        "(SELECT SUM(CASE WHEN movement_type = 'credit' THEN amount ELSE -amount END) FROM accounts_movements WHERE account_id = a.id) as movements_balance",
    ];

    const BALANCES_TABLE_NAME = 'accounts_balances';

    const BALANCES_TABLE_ALIAS = 'b';

    const BALANCES_ACCOUNT_ID_COLUMN = 'account_id';

    const BALANCES_REFERENCE_DATE_COLUMN = 'reference_date';

    const BALANCES_ACCOUNT_ID_COLUMN_ALIASED = 'b.account_id';

    const BALANCES_REFERENCE_DATE_COLUMN_ALIASED = 'b.reference_date';

    const DISPLAY_SELECT_MONTH_SUMMARY = [
        'a.id as account_id',
        'a.bank_name',
        'a.account_type',
        "SUM(CASE WHEN m.movement_type = 'credit' THEN m.amount ELSE 0 END) as total_credits",
        "SUM(CASE WHEN m.movement_type = 'debit' THEN m.amount ELSE 0 END) as total_debits",
        "SUM(CASE WHEN m.movement_type = 'credit' THEN 1 ELSE 0 END) as credit_count",
        "SUM(CASE WHEN m.movement_type = 'debit' THEN 1 ELSE 0 END) as debit_count",
        'f.status as file_process_status',
    ];

    const DISPLAY_SELECT_MONTH_SUMMARY_BALANCES = [
        'b.previous_month_balance',
        'b.current_month_balance',
    ];

    const FILES_TABLE_ALIAS = 'f';

    const FILES_ACCOUNT_ID_COLUMN_ALIASED = 'f.account_id';

    const FILES_REFERENCE_DATE_COLUMN_ALIASED = 'f.reference_date';

    const FILES_STATUS_COLUMN_ALIASED = 'f.status';

    const FILES_DELETED_COLUMN_ALIASED = 'f.deleted';

    const DISPLAY_SELECT_CONCILIATION_STATUS = [
        'a.id as account_id',
        'a.bank_name',
        'a.account_type',
    ];

    const DISPLAY_SELECT_CONCILIATION_COUNTS = [
        "SUM(CASE WHEN m.movement_type = 'credit' AND m.conciliated_status = 'conciliated' THEN 1 ELSE 0 END) as credit_conciliated",
        "SUM(CASE WHEN m.movement_type = 'credit' AND (m.conciliated_status = 'not_found' OR m.conciliated_status IS NULL) THEN 1 ELSE 0 END) as credit_not_conciliated",
        "SUM(CASE WHEN m.movement_type = 'debit' AND m.conciliated_status = 'conciliated' THEN 1 ELSE 0 END) as debit_conciliated",
        "SUM(CASE WHEN m.movement_type = 'debit' AND (m.conciliated_status = 'not_found' OR m.conciliated_status IS NULL) THEN 1 ELSE 0 END) as debit_not_conciliated",
    ];

    const DISPLAY_SELECT_RECENT_MOVEMENTS = [
        'id',
        'movement_type',
        'movement_date',
        'transaction_type',
        'description',
        'amount',
    ];

    const DISPLAY_SELECT_PENDING_FILES = [
        'accounts_files.id as id',
        'accounts_files.account_id as account_id',
        'accounts.bank_name as bank_name',
        'accounts_files.original_filename as original_filename',
        'accounts_files.reference_date as reference_date',
        'accounts_files.status as status',
        'accounts_files.error_message as error_message',
        'accounts_files.created_at as created_at',
    ];

    /**
     * Convert columns with aggregations to DB::raw
     */
    private function convertAggregationsToRaw(array $columns): array
    {
        return array_map(function ($col) {
            return str_contains($col, 'SUM(') || str_contains($col, 'MAX(') || str_contains($col, 'SELECT')
                ? DB::raw($col)
                : $col;
        }, $columns);
    }

    /**
     * Get accounts with current balance and last movement date
     *
     * @throws BindingResolutionException
     */
    public function getAccountsIndicators(): Collection
    {
        $query = function () {
            $selectColumns = array_merge(
                self::DISPLAY_SELECT_ACCOUNTS_INDICATORS,
                self::DISPLAY_SELECT_LAST_MOVEMENT_DATE,
                self::DISPLAY_SELECT_MOVEMENTS_BALANCE
            );

            $selectColumns = $this->convertAggregationsToRaw($selectColumns);

            $result = DB::table(self::TABLE_NAME.' as '.self::TABLE_ALIAS)
                ->select($selectColumns)
                ->where(self::ACTIVATED_COLUMN_ALIASED, BaseRepository::OPERATORS['EQUALS'], true)
                ->orderBy(self::BANK_NAME_COLUMN_ALIASED)
                ->get();

            return $result->map(function ($item) {
                $initialBalance = (float) ($item->initial_balance ?? 0);
                $movementsBalance = (float) ($item->movements_balance ?? 0);

                return AccountIndicatorData::fromResponse([
                    'account_id' => $item->account_id,
                    'bank_name' => $item->bank_name,
                    'account_type' => $item->account_type,
                    'current_balance' => $initialBalance + $movementsBalance,
                    'last_movement_date' => $item->last_movement_date,
                ]);
            });
        };

        return $this->doQuery($query);
    }

    /**
     * Get month summary (credits, debits, counts) grouped by account
     *
     * @throws BindingResolutionException
     */
    public function getMonthSummary(string $referenceDate): Collection
    {
        $movementAlias = 'm';
        $movementAccountIdAliased = $movementAlias.'.'.AccountMovementsRepository::ACCOUNT_ID_COLUMN;
        $movementDateAliased = $movementAlias.'.'.AccountMovementsRepository::MOVEMENT_DATE_COLUMN;

        $query = function () use ($referenceDate, $movementAlias, $movementAccountIdAliased, $movementDateAliased) {
            $selectColumns = array_merge(
                self::DISPLAY_SELECT_MONTH_SUMMARY,
                self::DISPLAY_SELECT_MONTH_SUMMARY_BALANCES,
                [self::FILES_REFERENCE_DATE_COLUMN_ALIASED.' as reference_date']
            );

            $selectColumns = $this->convertAggregationsToRaw($selectColumns);

            $result = DB::table(self::TABLE_NAME.' as '.self::TABLE_ALIAS)
                ->select($selectColumns)
                ->leftJoin(AccountFilesRepository::TABLE_NAME.' as '.self::FILES_TABLE_ALIAS, function ($join) use ($referenceDate) {
                    $join->on(self::FILES_ACCOUNT_ID_COLUMN_ALIASED, BaseRepository::OPERATORS['EQUALS'], self::ID_COLUMN_ALIASED)
                        ->where(self::FILES_REFERENCE_DATE_COLUMN_ALIASED, BaseRepository::OPERATORS['EQUALS'], $referenceDate)
                        ->where(self::FILES_DELETED_COLUMN_ALIASED, BaseRepository::OPERATORS['EQUALS'], false);
                })
                ->leftJoin(AccountMovementsRepository::TABLE_NAME.' as '.$movementAlias, function ($join) use ($referenceDate, $movementAccountIdAliased, $movementDateAliased) {
                    $join->on($movementAccountIdAliased, BaseRepository::OPERATORS['EQUALS'], self::ID_COLUMN_ALIASED)
                        ->where($movementDateAliased, BaseRepository::OPERATORS['LIKE'], "{$referenceDate}%");
                })
                ->leftJoin(self::BALANCES_TABLE_NAME.' as '.self::BALANCES_TABLE_ALIAS, function ($join) use ($referenceDate) {
                    $join->on(self::BALANCES_ACCOUNT_ID_COLUMN_ALIASED, BaseRepository::OPERATORS['EQUALS'], self::ID_COLUMN_ALIASED)
                        ->where(self::BALANCES_REFERENCE_DATE_COLUMN_ALIASED, BaseRepository::OPERATORS['EQUALS'], $referenceDate);
                })
                ->where(self::ACTIVATED_COLUMN_ALIASED, BaseRepository::OPERATORS['EQUALS'], true)
                ->groupBy(
                    self::ID_COLUMN_ALIASED,
                    self::BANK_NAME_COLUMN_ALIASED,
                    self::ACCOUNT_TYPE_COLUMN_ALIASED,
                    'b.previous_month_balance',
                    'b.current_month_balance',
                    self::FILES_REFERENCE_DATE_COLUMN_ALIASED,
                    self::FILES_STATUS_COLUMN_ALIASED
                )
                ->orderBy(self::BANK_NAME_COLUMN_ALIASED)
                ->get();

            return $result->map(fn ($item) => MonthSummaryData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    /**
     * Get conciliation status by account for a given month
     *
     * @throws BindingResolutionException
     */
    public function getConciliationStatus(string $referenceDate): Collection
    {
        $movementAlias = 'm';
        $movementAccountIdAliased = $movementAlias.'.'.AccountMovementsRepository::ACCOUNT_ID_COLUMN;
        $movementDateAliased = $movementAlias.'.'.AccountMovementsRepository::MOVEMENT_DATE_COLUMN;

        $query = function () use ($referenceDate, $movementAlias, $movementAccountIdAliased, $movementDateAliased) {
            $selectColumns = array_merge(
                self::DISPLAY_SELECT_CONCILIATION_STATUS,
                self::DISPLAY_SELECT_CONCILIATION_COUNTS
            );

            $selectColumns = $this->convertAggregationsToRaw($selectColumns);

            $result = DB::table(self::TABLE_NAME.' as '.self::TABLE_ALIAS)
                ->select($selectColumns)
                ->selectRaw('? as reference_month', [$referenceDate])
                ->leftJoin(AccountMovementsRepository::TABLE_NAME.' as '.$movementAlias, function ($join) use ($referenceDate, $movementAccountIdAliased, $movementDateAliased) {
                    $join->on($movementAccountIdAliased, BaseRepository::OPERATORS['EQUALS'], self::ID_COLUMN_ALIASED)
                        ->where($movementDateAliased, BaseRepository::OPERATORS['LIKE'], "{$referenceDate}%");
                })
                ->where(self::ACTIVATED_COLUMN_ALIASED, BaseRepository::OPERATORS['EQUALS'], true)
                ->groupBy(self::ID_COLUMN_ALIASED, self::BANK_NAME_COLUMN_ALIASED, self::ACCOUNT_TYPE_COLUMN_ALIASED)
                ->orderBy(self::BANK_NAME_COLUMN_ALIASED)
                ->get();

            return $result->map(fn ($item) => ConciliationStatusData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    /**
     * Get recent movements grouped by account (30 movements per account)
     *
     * @throws BindingResolutionException
     */
    public function getRecentMovements(int $limit = 30): Collection
    {
        $query = function () use ($limit) {
            $accounts = DB::table(self::TABLE_NAME)
                ->select(['id', 'bank_name', 'account_type'])
                ->where(self::ACTIVATED_COLUMN, BaseRepository::OPERATORS['EQUALS'], true)
                ->orderBy(self::BANK_NAME_COLUMN)
                ->get();

            return $accounts->map(function ($account) use ($limit) {
                $movements = DB::table(AccountMovementsRepository::TABLE_NAME)
                    ->select(self::DISPLAY_SELECT_RECENT_MOVEMENTS)
                    ->where(AccountMovementsRepository::ACCOUNT_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $account->id)
                    ->orderByDesc(AccountMovementsRepository::MOVEMENT_DATE_COLUMN)
                    ->orderByDesc('id')
                    ->limit($limit)
                    ->get()
                    ->map(fn ($item) => RecentMovementData::fromResponse((array) $item));

                return AccountRecentMovementsData::fromResponse((array) $account, $movements);
            });
        };

        return $this->doQuery($query);
    }

    /**
     * Get pending files (files with status different from movements_done)
     *
     * @throws BindingResolutionException
     */
    public function getPendingFiles(): Collection
    {
        $query = function () {
            $result = DB::table(AccountFilesRepository::TABLE_NAME)
                ->select(self::DISPLAY_SELECT_PENDING_FILES)
                ->join(
                    AccountRepository::TABLE_NAME,
                    AccountRepository::ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    AccountFilesRepository::ACCOUNT_ID_COLUMN_JOINED
                )
                ->where(AccountFilesRepository::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], false)
                ->whereNotIn(AccountFilesRepository::STATUS_COLUMN, [
                    AccountFilesRepository::MOVEMENTS_DONE,
                    AccountFilesRepository::CONCILIATION_DONE,
                ])
                ->where(AccountRepository::ACTIVATED_COLUMN, BaseRepository::OPERATORS['EQUALS'], true)
                ->orderByDesc(AccountFilesRepository::ID_COLUMN_JOINED)
                ->get();

            return $result->map(fn ($item) => PendingFileData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }
}
