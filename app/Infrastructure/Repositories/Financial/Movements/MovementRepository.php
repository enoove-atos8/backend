<?php

namespace Infrastructure\Repositories\Financial\Movements;


use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Domain\Financial\Movements\Models\Movement;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MovementRepository extends BaseRepository implements MovementRepositoryInterface
{
    protected mixed $model = Movement::class;

    const TABLE_NAME = 'movements';
    const ID_COLUMN = 'movements.id';
    const GROUP_ID_COLUMN = 'movements.group_id';
    const TYPE_COLUMN = 'movements.type';
    const AMOUNT_COLUMN = 'amount';
    const BALANCE_COLUMN = 'balance';
    const DESCRIPTION_COLUMN = 'description';
    const MOVEMENT_DATE_COLUMN = 'movement_date';
    const REFERENCE_COLUMN = 'reference';
    const IS_INITIAL_BALANCE_COLUMN = 'is_initial_balance';
    const DELETED_COLUMN = 'deleted';
    const PAGINATE_NUMBER = 30;

    const DISPLAY_SELECT_COLUMNS = [
        'movements.id as id',
        'movements.group_id as group_id',
        'movements.entry_id as entry_id',
        'movements.exit_id as exit_id',
        'movements.sub_type as sub_type',
        'movements.type as type',
        'movements.amount as amount',
        'movements.balance as balance',
        'movements.description as description',
        'movements.movement_date as movement_date',
        'movements.is_initial_balance as is_initial_balance',
        'movements.deleted as deleted',
    ];

    /**
     * Array of conditions
     */
    private array $queryConditions = [];



    /**
     * @param MovementsData $movementsData
     * @return Movement
     */
    public function createMovement(MovementsData $movementsData): Movement
    {
        return $this->create([
            'group_id' => $movementsData->groupId,
            'entry_id' => $movementsData->entryId,
            'exit_id' => $movementsData->exitId,
            'type' => $movementsData->type,
            'sub_type' => $movementsData->subType,
            'amount' => $movementsData->amount,
            'balance' => $movementsData->balance,
            'description' => $movementsData->description,
            'movement_date' => $movementsData->movementDate,
            'is_initial_balance' => $movementsData->isInitialBalance,
            'deleted' => $movementsData->deleted,
        ]);
    }


    /**
     * @param int $groupId
     * @return bool
     * @throws BindingResolutionException
     */
    public function resetBalance(int $groupId): bool
    {
        $conditions =[
            [
                'field' => self::GROUP_ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $groupId,
            ]
        ];

        return $this->update($conditions, [
            'deleted' => 1,
        ]);
    }


    /**
     * Get movements by group and optionally return indicators (entries, exits, current balance)
     *
     * @param int $groupId
     * @param string|null $dates
     * @param bool $paginate
     * @param bool $forIndicators Quando true, retorna formato adequado para indicadores
     * @return Collection|Paginator
     * @throws BindingResolutionException
     */
    public function getMovementsByGroup(int $groupId, ?string $dates = 'all', bool $paginate = true, bool $forIndicators = false): Collection | Paginator
    {
        $arrDates = [];

        if($dates != 'all' && $dates != null) {
            $arrDates = explode(',', $dates);
        }

        $query = function () use (
            $groupId,
            $arrDates,
            $paginate,
            $forIndicators) {

            $q = DB::table(MovementRepository::TABLE_NAME)
                ->where(self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 0)
                ->where(self::GROUP_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $groupId);

            if(isset($arrDates) && count($arrDates) > 0) {
                $q->where(function ($query) use ($arrDates) {
                    foreach ($arrDates as $date) {
                        $query->orWhere(self::MOVEMENT_DATE_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$date}%");
                    }
                });
            }


            if (!$forIndicators)
            {
                $q->orderBy(self::MOVEMENT_DATE_COLUMN)
                  ->orderBy(self::ID_COLUMN);
            }


            if ($forIndicators || !$paginate)
            {
                $result = $q->get();
                return collect($result)->map(fn($item) => MovementsData::fromResponse((array) $item));
            }
            else
            {
                $paginator = $q->simplePaginate(self::PAGINATE_NUMBER);
                return $paginator->setCollection($paginator->getCollection()->map(fn($item) => MovementsData::fromResponse((array) $item)));
            }
        };

        return $this->doQuery($query);
    }

    /**
     * Get initial balance movement for a group
     *
     * @param int $groupId
     * @return MovementsData|null
     * @throws BindingResolutionException
     * @throws UnknownProperties
     */
    public function getInitialMovementsByGroup(int $groupId): ?MovementsData
    {
        $this->queryConditions = [];

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, 0, 'and');
        $this->queryConditions[] = $this->whereEqual(self::GROUP_ID_COLUMN, $groupId, 'and');
        $this->queryConditions[] = $this->whereEqual(self::IS_INITIAL_BALANCE_COLUMN, 1, 'and');

        $result = $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN
        )->first();

        return MovementsData::fromResponse((array) $result);
    }




    /**
     * @param int $id
     * @return mixed
     * @throws BindingResolutionException
     */
    public function deleteMovement(int $id): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $id,
            ];

        return $this->update($conditions, [
            'deleted'  =>   1,
        ]);
    }

    /**
     * Mark all movements of a group as deleted
     *
     * @param int $groupId
     * @return mixed
     * @throws BindingResolutionException
     */
    public function deleteMovementsOfGroup(int $groupId): mixed
    {
        $conditions =[
            [
                'field' => self::GROUP_ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $groupId,
            ],
            [
                'field' => self::IS_INITIAL_BALANCE_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => 0,
            ]
        ];

        return $this->update($conditions, [
            'deleted' => 1,
        ]);
    }


    /**
     * @param int|null $entryId
     * @param int|null $exitId
     * @return mixed
     * @throws BindingResolutionException
     */
    public function deleteMovementByEntryOrExitId(int $entryId = null, int $exitId = null): mixed
    {
        $conditions =[
            [
                'field' => !is_null($entryId) ? EntryRepository::ENTRY_TYPE : ExitRepository::EXIT_TYPE,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => !is_null($entryId) ? $entryId : $exitId,
            ],
        ];

        return $this->update($conditions, [
            'deleted' => 1,
        ]);
    }

    /**
     * Get deleted movements for a group
     *
     * @param int $groupId
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getDeletedMovementsByGroup(int $groupId): Collection
    {
        $this->queryConditions = [];
        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, 1, 'and');
        $this->queryConditions[] = $this->whereEqual(self::GROUP_ID_COLUMN, $groupId, 'and');

        return $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::MOVEMENT_DATE_COLUMN
        );
    }



    /**
     * @param int $referenceId
     * @return Movement|null
     */
    public function getMovementByReference(int $referenceId): ?Movement
    {
        // TODO: Implement getMovementByReference() method.
    }

    /**
     * Get the balance from a movement
     *
     * @param Movement|null $movement
     * @return float
     */
    public function getMovementBalance(?Movement $movement): float
    {
        return $movement ? (float)$movement->balance : 0.0;
    }

    /**
     * Get the amount from a movement
     *
     * @param Movement|null $movement
     * @return float
     */
    public function getMovementAmount(?Movement $movement): float
    {
        return $movement ? (float)$movement->amount : 0.0;
    }


    /**
     * @param int $movementId
     * @param float $newBalance
     * @return void
     * @throws BindingResolutionException
     */
    public function updateMovementBalance(int $movementId, float $newBalance): void
    {
        $conditions =[
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $movementId,
            ]
        ];

        $this->update($conditions, [
            'balance' => $newBalance,
        ]);
    }


    public function addInitialBalance(MovementsData $movementsData): Movement
    {
        return $this->create([
            'group_id'             =>   $movementsData->groupId,
            'entry_id'             =>   $movementsData->entryId,
            'exit_id'              =>   $movementsData->exitId,
            'type'                 =>   $movementsData->type,
            'sub_type'             =>   $movementsData->subType,
            'amount'               =>   $movementsData->amount,
            'balance'              =>   $movementsData->balance,
            'description'          =>   $movementsData->description,
            'movement_date'        =>   $movementsData->movementDate,
            'is_initial_balance'   =>   $movementsData->isInitialBalance,
            'deleted'              =>   $movementsData->deleted,
        ]);
    }
}
