<?php

namespace Infrastructure\Repositories\Ecclesiastical\Groups\AmountRequests;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReminderConstants;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestReminderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AmountRequestReminderRepository implements AmountRequestReminderRepositoryInterface
{
    /**
     * Get reminder by ID
     */
    public function getById(int $id): ?AmountRequestReminderData
    {
        $reminder = DB::table(ReminderConstants::TABLE)
            ->where(ReminderConstants::COLUMN_ID, $id)
            ->first();

        if (! $reminder) {
            return null;
        }

        $reminderArray = (array) $reminder;

        // Decodifica JSON do metadata se existir
        if (isset($reminderArray[ReminderConstants::COLUMN_METADATA]) && is_string($reminderArray[ReminderConstants::COLUMN_METADATA])) {
            $reminderArray[ReminderConstants::COLUMN_METADATA] = json_decode($reminderArray[ReminderConstants::COLUMN_METADATA], true);
        }

        return AmountRequestReminderData::fromSelf($reminderArray);
    }

    /**
     * Update reminder status
     */
    public function updateStatus(
        int $id,
        string $status,
        ?string $errorMessage = null,
        ?array $metadata = null
    ): bool {
        $updateData = [
            ReminderConstants::COLUMN_STATUS => $status,
            ReminderConstants::COLUMN_UPDATED_AT => now(),
        ];

        if ($status === ReminderConstants::STATUS_SENT) {
            $updateData[ReminderConstants::COLUMN_SENT_AT] = now();
        }

        if ($errorMessage !== null) {
            $updateData[ReminderConstants::COLUMN_ERROR_MESSAGE] = $errorMessage;
        }

        if ($metadata !== null) {
            $updateData[ReminderConstants::COLUMN_METADATA] = json_encode($metadata);
        }

        return DB::table(ReminderConstants::TABLE)
            ->where(ReminderConstants::COLUMN_ID, $id)
            ->update($updateData) > 0;
    }
}
