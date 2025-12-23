<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new amount request
     *
     * @throws GeneralExceptions
     */
    public function execute(AmountRequestData $data): int
    {
        // Validate that no open request exists for this group
        if ($data->groupId !== null) {
            $existingOpenRequest = $this->repository->getOpenByGroupId($data->groupId);

            if ($existingOpenRequest !== null) {
                throw new GeneralExceptions(ReturnMessages::GROUP_HAS_OPEN_REQUEST, 400);
            }
        }

        $id = $this->repository->create($data);

        if ($id === 0) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_AMOUNT_REQUEST, 500);
        }

        return $id;
    }
}
