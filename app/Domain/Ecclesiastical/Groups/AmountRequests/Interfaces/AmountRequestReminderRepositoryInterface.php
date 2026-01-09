<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Interfaces;

use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;

interface AmountRequestReminderRepositoryInterface
{
    /**
     * Get reminder by ID
     */
    public function getById(int $id): ?AmountRequestReminderData;

    /**
     * Update reminder status
     */
    public function updateStatus(
        int $id,
        string $status,
        ?string $errorMessage = null,
        ?array $metadata = null
    ): bool;
}
