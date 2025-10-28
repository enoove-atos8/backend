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
     */
    public function createMovement(MovementsData $movementsData): Movement;

    /**
     * Create a new Movement record
     */
    public function resetBalance(int $groupId): bool;

    /**
     * Get Movements by group ID and optionally format for indicators
     */
    public function getMovementsByGroup(int $groupId, ?string $dates, bool $paginate = true, bool $forIndicators = false): Collection|Paginator;

    /**
     * Get initial balance movement for a group
     *
     * @return Movement|null
     */
    public function getInitialMovementsByGroup(int $groupId): ?MovementsData;

    /**
     * Get movement by entry id
     *
     * @return Movement|null
     */
    public function getMovementsByEntryIdAction(int $entryId): ?MovementsData;

    /**
     * Get movement by exit id
     *
     * @return Movement|null
     */
    public function getMovementsByExitIdAction(int $exitId): ?MovementsData;

    /**
     * Get Movement by reference ID
     */
    public function getMovementByReference(int $referenceId): ?Movement;

    /**
     * Delete a Movement record
     */
    public function deleteMovement(int $id): mixed;

    /**
     * Mark all movements of a group as deleted
     */
    public function deleteMovementsOfGroup(int $groupId): mixed;

    /**
     * Delete movement by entry id or exit id
     */
    public function deleteMovementByEntryOrExitId(?int $entryId = null, ?int $exitId = null): mixed;

    /**
     * Get total amount of deleted movements for a group
     * This calculates the final balance of all deleted movements
     *
     * @return float
     */
    public function getDeletedMovementsByGroup(int $groupId): Collection;

    /**
     * Get the balance from a movement
     */
    public function getMovementBalance(?Movement $movement): float;

    /**
     * Get the amount from a movement
     */
    public function getMovementAmount(?Movement $movement): float;

    /**
     * Get the amount from a movement
     */
    public function addInitialBalance(MovementsData $movementsData): Movement;

    public function updateMovementBalance(int $movementId, float $newBalance): void;

    public function updateMovementAmount(int $movementId, float $newAmount): void;
}
