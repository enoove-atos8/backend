<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Constants\ReturnMessages;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Infrastructure\Exceptions\GeneralExceptions;
use Illuminate\Support\Collection;

class GetMovementsByGroupAction
{
    /**
     * @var MovementRepositoryInterface
     */
    private MovementRepositoryInterface $movementRepository;

    /**
     * GetMovementsByGroupAction constructor.
     *
     * @param MovementRepositoryInterface $movementRepository
     */
    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * Execute the action to get movements by group
     *
     * @param int $groupId
     * @param string $dates
     * @return Collection|Paginator
     * @throws GeneralExceptions
     */
    public function execute(int $groupId, string $dates):  Collection | Paginator
    {
        $movements = $this->movementRepository->getMovementsByGroup($groupId, $dates);

        if (!$movements->isEmpty())
        {
            return $movements;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::MOVEMENTS_NOT_FOUND, 404);
        }

    }
}
