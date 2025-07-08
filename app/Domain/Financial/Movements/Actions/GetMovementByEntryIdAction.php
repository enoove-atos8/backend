<?php

namespace App\Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Constants\ReturnMessages;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Infrastructure\Exceptions\GeneralExceptions;
use Illuminate\Support\Collection;

class GetMovementByEntryIdAction
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
     * @param int $entryId
     * @return MovementsData|null
     */
    public function execute(int $entryId): ?MovementsData
    {
        return $this->movementRepository->getMovementsByEntryIdAction($entryId);
    }
}
