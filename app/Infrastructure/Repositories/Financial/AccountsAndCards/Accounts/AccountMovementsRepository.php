<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Accounts;

use App\Domain\Financial\AccountsAndCards\Accounts\Models\AccountsMovements;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountMovementsData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class AccountMovementsRepository extends BaseRepository implements AccountMovementsRepositoryInterface
{
    protected mixed $model = AccountsMovements::class;

    const TABLE_NAME = 'accounts_movements';

    /**
     * Create a single movement
     *
     * @param AccountMovementsData $accountMovementsData
     * @return mixed
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
     * @param Collection $movements Collection of ExtractorFileData
     * @param int $accountId
     * @param int $fileId
     * @return bool
     */
    public function bulkCreateMovements(Collection $movements, int $accountId, int $fileId): bool
    {
        $data = $movements->map(function($movement) use ($accountId, $fileId) {
            return [
                'account_id' => $accountId,
                'file_id' => $fileId,
                'movement_date' => $movement->movementDate,
                'transaction_type' => $movement->description,
                'description' => $movement->description,
                'amount' => $movement->amount,
                'movement_type' => $movement->type === 'C' ? 'credit' : 'debit',
                'anonymous' => false,
                'conciliated_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        return DB::table(self::TABLE_NAME)->insert($data);
    }

    /**
     * Delete movements by account and file
     *
     * @param int $accountId
     * @param int $fileId
     * @return bool
     */
    public function deleteByAccountAndFile(int $accountId, int $fileId): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where('account_id', $accountId)
            ->where('file_id', $fileId)
            ->delete() > 0;
    }
}
