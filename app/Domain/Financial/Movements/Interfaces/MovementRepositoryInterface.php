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
     * Create a new Movement record
     *
     * @param int $groupId
     * @return bool
     */
    public function resetBalance(int $groupId): bool;

    /**
     * Get Movements by group ID and optionally format for indicators
     *
     * @param int $groupId
     * @param string|null $dates
     * @param bool $paginate
     * @param bool $forIndicators
     * @return Collection|Paginator
     */
    public function getMovementsByGroup(int $groupId, ?string $dates, bool $paginate = true, bool $forIndicators = false): Collection | Paginator;

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
     * Delete movement by entry id or exit id
     *
     * @param int|null $entryId
     * @param int|null $exitId
     * @return mixed
     */
    public function deleteMovementByEntryOrExitId(int $entryId = null, int $exitId = null): mixed;



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



    public function updateMovementBalance(int $movementId, float $newBalance): void;
}
