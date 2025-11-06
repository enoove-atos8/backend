<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Accounts;

use App\Domain\Financial\AccountsAndCards\Accounts\Models\AccountsMovements;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\MovementsData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\MovementsRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class MovementsRepository extends BaseRepository implements MovementsRepositoryInterface
{
    protected mixed $model = AccountsMovements::class;

    const TABLE_NAME = 'accounts_movements';

    const ID_COLUMN_JOINED = 'accounts_movements.id';

    const ACCOUNT_ID_COLUMN_JOINED = 'accounts_movements.account_id';

    const MOVEMENT_DATE_COLUMN = 'accounts_movements.movement_date';

    const MOVEMENT_TYPE_COLUMN = 'movementType';

    const AMOUNT_COLUMN = 'amount';

    const DEBIT_VALUE = 'debit';

    const CREDIT_VALUE = 'credit';

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
                ->where(self::MOVEMENT_DATE_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$referenceDate}%")
                ->orderBy(self::MOVEMENT_DATE_COLUMN, 'asc')
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
}
