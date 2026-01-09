<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestReminderRepositoryInterface;

class UpdateReminderStatusAction
{
    public function __construct(
        private AmountRequestReminderRepositoryInterface $reminderRepository
    ) {}

    public function execute(
        int $reminderId,
        string $status,
        ?string $errorMessage = null,
        ?array $metadata = null
    ): bool {
        return $this->reminderRepository->updateStatus(
            $reminderId,
            $status,
            $errorMessage,
            $metadata
        );
    }
}
