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
     * Get Movements by group ID
     *
     * @param int $groupId
     * @return Collection
     */
    public function getMovementsByGroup(int $groupId): Collection;

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
}
