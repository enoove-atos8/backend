<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Actions\GetApprovedAmountRequestByGroupAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\LinkExitToApprovedAmountRequestAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages as AmountRequestMessages;
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

    private GetApprovedAmountRequestByGroupAction $getApprovedAmountRequestByGroupAction;

    private LinkExitToApprovedAmountRequestAction $linkExitToApprovedAmountRequestAction;

    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface,
        MovementRepositoryInterface $movementRepositoryInterface,
        MovementsData $movementsData,
        CreateMovementAction $createMovementAction,
        GetApprovedAmountRequestByGroupAction $getApprovedAmountRequestByGroupAction,
        LinkExitToApprovedAmountRequestAction $linkExitToApprovedAmountRequestAction
    ) {
        $this->exitRepository = $exitRepositoryInterface;
        $this->movementRepository = $movementRepositoryInterface;
        $this->movementsData = $movementsData;
        $this->createMovementAction = $createMovementAction;
        $this->getApprovedAmountRequestByGroupAction = $getApprovedAmountRequestByGroupAction;
        $this->linkExitToApprovedAmountRequestAction = $linkExitToApprovedAmountRequestAction;
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
            $isTransferType = $exitData->exitType == ExitRepository::TRANSFER_VALUE
                || $exitData->exitType == ExitRepository::MINISTERIAL_TRANSFER_VALUE;

            if ($isTransferType) {
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

        // Map exit type to amount request type
        $requestType = $this->mapExitTypeToRequestType($exitData->exitType);

        if ($requestType === null) {
            return;
        }

        // Check if there's an approved amount request for this group and type
        $amountRequest = $this->getApprovedAmountRequestByGroupAction->execute($groupId, $requestType);

        if ($amountRequest === null) {
            return;
        }

        // Link the exit to the amount request
        $this->linkExitToApprovedAmountRequestAction->execute($amountRequest->id, $exitId);
    }

    /**
     * Map exit type to amount request type
     */
    private function mapExitTypeToRequestType(?string $exitType): ?string
    {
        return match ($exitType) {
            ExitRepository::TRANSFER_VALUE => AmountRequestMessages::TYPE_GROUP_FUND,
            ExitRepository::MINISTERIAL_TRANSFER_VALUE => AmountRequestMessages::TYPE_MINISTERIAL_INVESTMENT,
            default => null,
        };
    }
}
