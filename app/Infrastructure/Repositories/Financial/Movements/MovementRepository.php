<?php

namespace Infrastructure\Repositories\Financial\Movements;


use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Domain\Financial\Movements\Models\Movement;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

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
    const MOVEMENT_DATE_ORDER_COLUMN = 'movement_date';
    const DELETED_COLUMN = 'deleted';

    const DISPLAY_SELECT_COLUMNS = [
        'movements.id as id',
        'movements.group_id as group_id',
        'movements.reference_id as reference_id',
        'movements.sub_type as sub_type',
        'movements.type as type',
        'movements.amount as amount',
        'movements.balance as balance',
        'movements.description as description',
        'movements.movement_date as movement_date',
        'movements.is_initial_balance as is_initial_balance',
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
            'reference_id' => $movementsData->referenceId,
            'type' => $movementsData->type,
            'subType' => $movementsData->subType,
            'amount' => $movementsData->amount,
            'balance' => $movementsData->balance,
            'description' => $movementsData->description,
            'movement_date' => $movementsData->movementDate,
            'is_initial_balance' => $movementsData->isInitialBalance,
        ]);
    }




    /**
     *
     * @param int $groupId
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getMovementsByGroup(int $groupId): Collection
    {
        $this->queryConditions = [];

        $this->queryConditions[] = $this->whereEqual(self::GROUP_ID_COLUMN, $groupId, 'and');
        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, 0, 'and');

        $balances = $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::MOVEMENT_DATE_ORDER_COLUMN
        );

        return $balances->map(fn($balance) => MovementsData::fromArray((array) $balance));
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
     * @param int $referenceId
     * @return Movement|null
     */
    public function getMovementByReference(int $referenceId): ?Movement
    {
        // TODO: Implement getMovementByReference() method.
    }
}
