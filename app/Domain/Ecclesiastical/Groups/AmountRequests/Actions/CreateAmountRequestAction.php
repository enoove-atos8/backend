<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
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
        }

        $id = $this->repository->create($data);

        if ($id === 0) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_AMOUNT_REQUEST, 500);
        }

        return $id;
    }
}
