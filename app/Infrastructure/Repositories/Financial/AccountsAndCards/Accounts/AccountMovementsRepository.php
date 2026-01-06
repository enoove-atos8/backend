<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Accounts;

use App\Domain\Financial\AccountsAndCards\Accounts\Models\AccountsMovements;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountMovementsData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\MovementsData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class AccountMovementsRepository extends BaseRepository implements AccountMovementsRepositoryInterface
{
    protected mixed $model = AccountsMovements::class;

    const TABLE_NAME = 'accounts_movements';

    const ACCOUNT_ID_COLUMN = 'account_id';

    const FILE_ID_COLUMN = 'file_id';

    const MOVEMENT_DATE_COLUMN = 'movement_date';

    const MOVEMENT_TYPE_COLUMN = 'movement_type';

    const AMOUNT_COLUMN = 'amount';

    const CONCILIATED_STATUS_COLUMN = 'conciliated_status';

    const ID_COLUMN = 'id';

    const MOVEMENT_TYPE_CREDIT = 'credit';

    const MOVEMENT_TYPE_DEBIT = 'debit';

    const STATUS_CONCILIATED = 'conciliated';

    const STATUS_MOVEMENT_NOT_FOUND = 'not_found';

    const ENTRIES_TABLE = 'entries';

    const EXITS_TABLE = 'exits';

    const CULTS_TABLE = 'cults';

    const DELETED_COLUMN = 'deleted';

    const CULT_ID_COLUMN = 'cult_id';

    const DATE_TRANSACTION_COMPENSATION_COLUMN = 'date_transaction_compensation';

    // Constantes do MovementsRepository
    const ID_COLUMN_JOINED = 'accounts_movements.id';

    const ACCOUNT_ID_COLUMN_JOINED = 'accounts_movements.account_id';

    const MOVEMENT_DATE_COLUMN_JOINED = 'accounts_movements.movement_date';

    const PAGINATE_NUMBER = 30;

    const DISPLAY_SELECT_COLUMNS = [
        'accounts_movements.id as accounts_movements_id',
        'accounts_movements.account_id as accounts_movements_account_id',
        'accounts_movements.file_id as accounts_movements_file_id',
        'accounts_movements.movement_date as accounts_movements_movement_date',
        'accounts_movements.transaction_type as accounts_movements_transaction_type',
        'accounts_movements.description as accounts_movements_description',
        'accounts_movements.amount as accounts_movements_amount',
        'accounts_movements.movement_type as accounts_movements_movement_type',
        'accounts_movements.anonymous as accounts_movements_anonymous',
        'accounts_movements.conciliated_status as accounts_movements_conciliated_status',
        'accounts_movements.created_at as accounts_movements_created_at',
        'accounts_movements.updated_at as accounts_movements_updated_at',
    ];

    /**
     * Create a single movement
     */
    public function createMovement(AccountMovementsData $accountMovementsData): mixed
    {
        return $this->create([
            'account_id' => $accountMovementsData->accountId,
            'file_id' => $accountMovementsData->fileId,
            'movement_date' => $accountMovementsData->movementDate,
            'transaction_type' => $accountMovementsData->transactionType,
            'description' => $accountMovementsData->description,
            'amount' => $accountMovementsData->amount,
            'movement_type' => $accountMovementsData->movementType,
            'anonymous' => $accountMovementsData->anonymous,
            'conciliated_status' => $accountMovementsData->conciliatedStatus,
        ]);
    }

    /**
     * Create multiple movements in bulk
     *
     * @param  Collection  $movements  Collection of ExtractorFileData
     */
    public function bulkCreateMovements(Collection $movements, int $accountId, int $fileId): bool
    {
        $data = $movements->map(function ($movement) use ($accountId, $fileId) {
            return [
                self::ACCOUNT_ID_COLUMN => $accountId,
                self::FILE_ID_COLUMN => $fileId,
                self::MOVEMENT_DATE_COLUMN => $movement->movementDate,
                'transaction_type' => $movement->description,
                'description' => $movement->description,
                self::AMOUNT_COLUMN => $movement->amount,
                self::MOVEMENT_TYPE_COLUMN => $movement->type === 'C' ? self::MOVEMENT_TYPE_CREDIT : self::MOVEMENT_TYPE_DEBIT,
                'anonymous' => false,
                self::CONCILIATED_STATUS_COLUMN => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        return DB::table(self::TABLE_NAME)->insert($data);
    }

    /**
     * Delete movements by account and file
     */
    public function deleteByAccountAndFile(int $accountId, int $fileId): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ACCOUNT_ID_COLUMN, $accountId)
            ->where(self::FILE_ID_COLUMN, $fileId)
            ->delete() > 0;
    }

    /**
     * Delete movements by account and reference date (month)
     *
     * @param  string  $referenceDate  Format: Y-m
     */
    public function deleteByAccountAndReferenceDate(int $accountId, string $referenceDate): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ACCOUNT_ID_COLUMN, $accountId)
            ->where(self::MOVEMENT_DATE_COLUMN, BaseRepository::OPERATORS['LIKE'], "{$referenceDate}%")
            ->delete() > 0;
    }

    /**
     * Get movements by account id and reference date
     *
     * @throws BindingResolutionException
     */
    public function getMovements(int $accountId, string $referenceDate, bool $paginate = true): Collection|Paginator
    {
        $query = function () use ($accountId, $referenceDate, $paginate) {
            $q = DB::table(self::TABLE_NAME)
                ->select(self::DISPLAY_SELECT_COLUMNS)
                ->where(self::ACCOUNT_ID_COLUMN_JOINED, BaseRepository::OPERATORS['EQUALS'], $accountId)
                ->where(self::MOVEMENT_DATE_COLUMN_JOINED, BaseRepository::OPERATORS['LIKE'], "%{$referenceDate}%")
                ->orderBy(self::MOVEMENT_DATE_COLUMN_JOINED, 'asc')
                ->orderBy(self::ID_COLUMN_JOINED, 'asc');

            if (! $paginate) {
                $result = $q->get();

                return collect($result)->map(fn ($item) => MovementsData::fromResponse((array) $item));
            } else {
                $paginator = $q->simplePaginate(self::PAGINATE_NUMBER);

                return $paginator->setCollection($paginator->getCollection()->map(fn ($item) => MovementsData::fromResponse((array) $item)));
            }
        };

        return $this->doQuery($query);
    }

    /**
     * Get movements by account and file
     */
    public function getMovementsByAccountAndFile(int $accountId, int $fileId): Collection
    {
        $result = DB::table(self::TABLE_NAME)
            ->select(self::DISPLAY_SELECT_COLUMNS)
            ->where(self::ACCOUNT_ID_COLUMN, $accountId)
            ->where(self::FILE_ID_COLUMN, $fileId)
            ->orderBy(self::MOVEMENT_DATE_COLUMN_JOINED, 'asc')
            ->orderBy(self::ID_COLUMN_JOINED, 'asc')
            ->get();

        return collect($result)->map(fn ($item) => MovementsData::fromResponse((array) $item));
    }

    /**
     * Bulk update conciliation status using CASE WHEN
     */
    public function bulkUpdateConciliationStatus(array $reconciliationMap): void
    {
        if (empty($reconciliationMap)) {
            return;
        }

        $ids = array_keys($reconciliationMap);
        $cases = [];
        $params = [];

        foreach ($reconciliationMap as $id => $status) {
            $cases[] = 'WHEN '.self::ID_COLUMN.' = ? THEN ?';
            $params[] = $id;
            $params[] = $status;
        }

        $caseStatement = implode(' ', $cases);
        $idsPlaceholder = implode(',', array_fill(0, count($ids), '?'));

        $sql = 'UPDATE '.self::TABLE_NAME.'
                SET '.self::CONCILIATED_STATUS_COLUMN." = CASE {$caseStatement} END
                WHERE ".self::ID_COLUMN." IN ({$idsPlaceholder})";

        $params = array_merge($params, $ids);

        DB::update($sql, $params);
    }
}
