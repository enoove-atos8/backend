<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Constants\ReturnMessages;
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
     * @return mixed
     * @throws GeneralExceptions
     */
    public function execute(int $groupId): mixed
    {
        $result = $this->movementRepository->deleteMovementsOfGroup($groupId);

        if($result)
        {
            return $result;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::MOVEMENTS_DELETE_ERROR, 500);
        }
    }
}
