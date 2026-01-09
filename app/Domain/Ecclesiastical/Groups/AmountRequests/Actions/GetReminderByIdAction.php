<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestReminderRepositoryInterface;

class GetReminderByIdAction
{
    public function __construct(
        private AmountRequestReminderRepositoryInterface $reminderRepository
    ) {}

    public function execute(int $reminderId): ?AmountRequestReminderData
    {
        return $this->reminderRepository->getById($reminderId);
    }
}
