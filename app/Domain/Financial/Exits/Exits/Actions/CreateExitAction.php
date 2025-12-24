<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Domain\Financial\Exits\Exits\Constants\ReturnMessages;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Domain\Financial\Exits\Exits\Models\Exits;
use Domain\Financial\Movements\Actions\CreateMovementAction;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreateExitAction
{
    private ExitRepositoryInterface $exitRepository;

    private MovementRepositoryInterface $movementRepository;

    private MovementsData $movementsData;

    private CreateMovementAction $createMovementAction;

    private AmountRequestRepositoryInterface $amountRequestRepository;

    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface,
        MovementRepositoryInterface $movementRepositoryInterface,
        MovementsData $movementsData,
        CreateMovementAction $createMovementAction,
        AmountRequestRepositoryInterface $amountRequestRepository
    ) {
        $this->exitRepository = $exitRepositoryInterface;
        $this->movementRepository = $movementRepositoryInterface;
        $this->movementsData = $movementsData;
        $this->createMovementAction = $createMovementAction;
        $this->amountRequestRepository = $amountRequestRepository;
    }

    /**
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function execute(ExitData $exitData): Exits
    {
        $dateExitRegister = $exitData->dateExitRegister;
        $dateTransactionCompensation = $exitData->dateTransactionCompensation;

        if (! is_null($dateTransactionCompensation)) {
            if (substr($dateExitRegister, 0, 7) !== substr($dateTransactionCompensation, 0, 7)) {
                $exitData->dateExitRegister = substr($dateTransactionCompensation, 0, 7).'-01';
            }
        }

        $exit = $this->exitRepository->newExit($exitData);
        $exitData->id = $exit->id;

        if (! is_null($exitData->id)) {
            if ($exitData->exitType == ExitRepository::TRANSFER_VALUE) {
                $movementData = $this->movementsData::fromObjectData(null, $exitData);
                $this->createMovementAction->execute($movementData);

                // Auto-link to approved amount request if exists
                $this->autoLinkAmountRequest($exitData, $exit->id);
            }

            return $exit;
        } else {
            throw new GeneralExceptions(ReturnMessages::CREATE_EXIT_ERROR, 500);
        }
    }

    /**
     * Auto-link exit to approved amount request for the group
     */
    private function autoLinkAmountRequest(ExitData $exitData, int $exitId): void
    {
        // Check if exit is for a group
        $groupId = $exitData->group->id ?? null;

        if ($groupId === null) {
            return;
        }

        // Check if there's an approved amount request for this group
        $amountRequest = $this->amountRequestRepository->getApprovedByGroupId($groupId);

        if ($amountRequest === null) {
            return;
        }

        // Link the exit to the amount request
        $this->amountRequestRepository->linkExit($amountRequest->id, $exitId);
    }
}
