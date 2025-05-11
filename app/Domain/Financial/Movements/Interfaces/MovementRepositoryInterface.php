<?php

namespace Domain\Financial\Movements\Interfaces;

use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Models\Movement;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface MovementRepositoryInterface
{
    /**
     * Create a new Movement record
     *
     * @param MovementsData $movementsData
     * @return Movement
     */
    public function createMovement(MovementsData $movementsData): Movement;

    /**
     * Get Movements by group ID (excluding initial balance)
     *
     * @param int $groupId
     * @param string|null $dates
     * @param bool $paginate
     * @return Collection|Paginator
     */
    public function getMovementsByGroup(int $groupId, ?string $dates, bool $paginate = true): Collection | Paginator;

    /**
     * Get movement indicators (entries, exits, current balance) by group
     *
     * @param int $groupId
     * @param string|null $dates
     * @return Collection
     */
    public function getMovementsIndicatorsByGroup(int $groupId, ?string $dates): Collection;

    /**
     * Get initial balance movement for a group
     *
     * @param int $groupId
     * @return Movement|null
     */
    public function getInitialMovementsByGroup(int $groupId): ?MovementsData;

    /**
     * Get Movement by reference ID
     *
     * @param int $referenceId
     * @return Movement|null
     */
    public function getMovementByReference(int $referenceId): ?Movement;

    /**
     * Delete a Movement record
     *
     * @param int $id
     * @return mixed
     */
    public function deleteMovement(int $id): mixed;

    /**
     * Mark all movements of a group as deleted
     *
     * @param int $groupId
     * @return mixed
     */
    public function deleteMovementsOfGroup(int $groupId): mixed;

    /**
     * Get total amount of deleted movements for a group
     * This calculates the final balance of all deleted movements
     *
     * @param int $groupId
     * @return float
     */
    public function getDeletedMovementsByGroup(int $groupId): Collection;

    /**
     * Get the balance from a movement
     *
     * @param Movement|null $movement
     * @return float
     */
    public function getMovementBalance(?Movement $movement): float;

    /**
     * Get the amount from a movement
     *
     * @param Movement|null $movement
     * @return float
     */
    public function getMovementAmount(?Movement $movement): float;


    /**
     * Get the amount from a movement
     *
     * @param MovementsData $movementsData
     * @return Movement
     */
    public function addInitialBalance(MovementsData $movementsData): Movement;
}
