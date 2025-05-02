<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteMovementsOfGroupAction
{
    private MovementRepositoryInterface $movementRepository;

    /**
     * Constructor
     *
     * @param MovementRepositoryInterface $movementRepository
     */
    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * Execute the action to delete all movements of a group
     *
     * @param int $groupId
     * @return void
     * @throws GeneralExceptions
     */
    public function execute(int $groupId): void
    {
        $result = $this->movementRepository->deleteMovementsOfGroup($groupId);

        if (!$result) {
            throw new GeneralExceptions("Não foi possível excluir as movimentações do grupo", 500);
        }
    }
}
