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
use Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

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
     * Get movements by group, excluding initial balance movements
     *
     * @param int $groupId
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getMovementsByGroup(int $groupId): Collection
    {
        $this->queryConditions = [];

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, 0, 'and');
        $this->queryConditions[] = $this->whereEqual(self::GROUP_ID_COLUMN, $groupId, 'and');
        $this->queryConditions[] = $this->whereEqual(self::IS_INITIAL_BALANCE_COLUMN, 0, 'and');

        return $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN
        );
    }

    /**
     * Get initial balance movement for a group
     *
     * @param int $groupId
     * @return Movement|null
     * @throws BindingResolutionException
     */
    public function getInitialMovementsByGroup(int $groupId): ?Movement
    {
        $this->queryConditions = [];

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, 0, 'and');
        $this->queryConditions[] = $this->whereEqual(self::GROUP_ID_COLUMN, $groupId, 'and');
        $this->queryConditions[] = $this->whereEqual(self::IS_INITIAL_BALANCE_COLUMN, 1, 'and');

        $result = $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN
        );

        return $result->isNotEmpty() ? $result->first() : null;
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
        $conditions =
            [
                'field' => self::GROUP_ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $groupId,
            ];

        return $this->update($conditions, [
            'deleted' => 1,
        ]);
    }

    /**
     * Get total amount of deleted movements for a group
     * This calculates the final balance of all deleted movements
     * 
     * @param int $groupId
     * @return float
     */
    public function getTotalAmountOfDeletedMovementsByGroup(int $groupId): float
    {
        // Buscar todas as movimentações que foram marcadas como deletadas para o grupo
        $this->queryConditions = [];
        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, 1, 'and');
        $this->queryConditions[] = $this->whereEqual(self::GROUP_ID_COLUMN, $groupId, 'and');
        
        $deletedMovements = $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::MOVEMENT_DATE_ORDER_COLUMN
        );
        
        // Se não houver movimentações deletadas, retorna zero
        if ($deletedMovements->isEmpty()) {
            return 0.0;
        }
        
        // Calcula o saldo final considerando todas as movimentações deletadas
        $totalBalance = 0.0;
        
        foreach ($deletedMovements as $movement) {
            $amount = $this->getMovementAmount($movement);
            
            // Se for movimentação de entrada (saldo inicial ou entrada comum)
            if ($movement->type === EntryRepository::ENTRY_TYPE || $movement->is_initial_balance) {
                $totalBalance += $amount;
            } 
            // Se for movimentação de saída
            else if ($movement->type === ExitRepository::EXIT_TYPE) {
                $totalBalance -= $amount;
            }
        }
        
        return $totalBalance;
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
}
