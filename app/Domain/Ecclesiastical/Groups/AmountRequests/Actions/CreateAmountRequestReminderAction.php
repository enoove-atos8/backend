<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateAmountRequestReminderAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a reminder for an amount request
     *
     * @throws GeneralExceptions
     */
    public function execute(AmountRequestReminderData $data): int
    {
        // Check if amount request exists
        $existing = $this->repository->getById($data->amountRequestId);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Create reminder
        $reminderId = $this->repository->createReminder($data);

        if ($reminderId === 0) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_REMINDER, 500);
        }

        return $reminderId;
    }
}
