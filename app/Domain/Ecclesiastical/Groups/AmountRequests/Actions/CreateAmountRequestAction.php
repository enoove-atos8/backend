<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Application\Core\Events\Ecclesiastical\Groups\AmountRequests\AmountRequestStatusChanged;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateAmountRequestAction
{
    public function __construct(
        private AmountRequestRepositoryInterface $repository,
        private GroupRepositoryInterface $groupRepository
    ) {}

    /**
     * Create a new amount request
     *
     * @throws GeneralExceptions
     */
    public function execute(AmountRequestData $data): int
    {
        if ($data->groupId !== null) {
            // Validate that no open request exists for this group
            $existingOpenRequest = $this->repository->getOpenByGroupId($data->groupId);

            if ($existingOpenRequest !== null) {
                throw new GeneralExceptions(ReturnMessages::GROUP_HAS_OPEN_REQUEST, 400);
            }

            // CONDITIONAL VALIDATION based on request type
            $requestType = $data->type ?? ReturnMessages::TYPE_GROUP_FUND;

            if ($requestType === ReturnMessages::TYPE_GROUP_FUND) {
                // Validate that the group has sufficient balance for the requested amount
                $groupBalance = $this->groupRepository->getGroupBalance($data->groupId);
                $currentBalance = $groupBalance?->balance ?? 0.0;
                $requestedAmount = (float) ($data->requestedAmount ?? 0.0);

                if ($currentBalance < $requestedAmount) {
                    throw new GeneralExceptions(
                        ReturnMessages::GROUP_INSUFFICIENT_BALANCE,
                        400,
                        null,
                        ['balance' => $currentBalance]
                    );
                }
            } elseif ($requestType === ReturnMessages::TYPE_MINISTERIAL_INVESTMENT) {
                // Validate ministerial investment limit
                $ministerialLimit = $this->groupRepository->getMinisterialInvestmentLimit($data->groupId);

                if ($ministerialLimit === null) {
                    throw new GeneralExceptions(ReturnMessages::GROUP_NO_MINISTERIAL_LIMIT, 400);
                }

                $requestedAmount = (float) ($data->requestedAmount ?? 0.0);
                $limit = (float) $ministerialLimit;

                if ($requestedAmount > $limit) {
                    throw new GeneralExceptions(
                        ReturnMessages::GROUP_LIMIT_EXCEEDED,
                        400,
                        null,
                        ['limit' => $limit]
                    );
                }
            } else {
                throw new GeneralExceptions(ReturnMessages::INVALID_REQUEST_TYPE, 400);
            }
        }

        $id = $this->repository->create($data);

        if ($id === 0) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_AMOUNT_REQUEST, 500);
        }

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $id,
            event: ReturnMessages::HISTORY_EVENT_CREATED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_CREATED],
            userId: $data->requestedBy,
            metadata: [
                ReturnMessages::METADATA_KEY_REQUESTED_AMOUNT => $data->requestedAmount,
                ReturnMessages::METADATA_KEY_GROUP_ID => $data->groupId,
            ]
        ));

        // Dispatch event for WhatsApp notification
        event(new AmountRequestStatusChanged(
            amountRequestId: $id,
            oldStatus: '',
            newStatus: ReturnMessages::STATUS_PENDING,
            userId: $data->requestedBy ?? 0,
            additionalData: [
                'requested_amount' => $data->requestedAmount,
                'group_id' => $data->groupId,
                'description' => $data->description,
                'proof_deadline' => $data->proofDeadline,
            ]
        ));

        return $id;
    }
}
