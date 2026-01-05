<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Interfaces;

use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestIndicatorsData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReceiptData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface AmountRequestRepositoryInterface
{
    /**
     * Get all amount requests with optional filters (paginated)
     *
     * @param  array  $filters  Optional filters (status, group_id, member_id, date_from, date_to)
     * @param  bool  $paginate  Whether to paginate results
     */
    public function getAll(array $filters = [], bool $paginate = true): Collection|Paginator;

    /**
     * Get a single amount request by ID
     */
    public function getById(int $id): ?AmountRequestData;

    /**
     * Create a new amount request
     */
    public function create(AmountRequestData $data): int;

    /**
     * Update an existing amount request
     */
    public function update(int $id, AmountRequestData $data): bool;

    /**
     * Soft delete an amount request
     */
    public function delete(int $id): bool;

    /**
     * Approve an amount request
     */
    public function approve(int $id, int $approvedBy): bool;

    /**
     * Reject an amount request
     */
    public function reject(int $id, int $approvedBy, string $rejectionReason): bool;

    /**
     * Mark an amount request as transferred
     */
    public function markAsTransferred(int $id, int $exitId): bool;

    /**
     * Link an exit to an amount request (status: approved -> transferred)
     */
    public function linkExit(int $id, int $exitId): bool;

    /**
     * Unlink an exit from an amount request (status: transferred -> approved)
     */
    public function unlinkExit(int $id): bool;

    /**
     * Get approved amount request by group ID (for auto-linking)
     */
    public function getApprovedByGroupId(int $groupId): ?AmountRequestData;

    /**
     * Update proven amount and status
     */
    public function updateProvenAmount(int $id, string $provenAmount, string $status): bool;

    /**
     * Close an amount request
     */
    public function close(int $id, int $closedBy): bool;

    /**
     * Update status
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Get all receipts for an amount request
     */
    public function getReceipts(int $amountRequestId): Collection;

    /**
     * Create a new receipt
     */
    public function createReceipt(AmountRequestReceiptData $data): int;

    /**
     * Soft delete a receipt
     */
    public function deleteReceipt(int $amountRequestId, int $receiptId): bool;

    /**
     * Calculate total proven amount from receipts
     */
    public function calculateProvenAmount(int $amountRequestId): string;

    /**
     * Get amount requests with overdue proof deadline
     */
    public function getOverdueRequests(): Collection;

    /**
     * Get amount requests approaching deadline
     *
     * @param  int  $daysUntilDeadline  Number of days until deadline
     */
    public function getRequestsApproachingDeadline(int $daysUntilDeadline): Collection;

    /**
     * Get indicators/summary for dashboard
     *
     * @param  int|null  $groupId  Optional group filter
     */
    public function getIndicators(?int $groupId = null): AmountRequestIndicatorsData;

    /**
     * Get all reminders for an amount request
     */
    public function getReminders(int $amountRequestId): Collection;

    /**
     * Create a new reminder
     */
    public function createReminder(AmountRequestReminderData $data): int;

    /**
     * Get history/timeline for an amount request
     */
    public function getHistory(int $amountRequestId): Collection;

    /**
     * Create a history record
     */
    public function createHistory(AmountRequestHistoryData $data): int;

    /**
     * Update an existing receipt
     */
    public function updateReceipt(int $amountRequestId, int $receiptId, AmountRequestReceiptData $data): bool;

    /**
     * Get a single receipt by ID
     */
    public function getReceiptById(int $amountRequestId, int $receiptId): ?AmountRequestReceiptData;

    /**
     * Get open amount request by group ID (status != closed)
     */
    public function getOpenByGroupId(int $groupId): ?AmountRequestData;

    /**
     * Link a devolution entry to an amount request
     */
    public function linkDevolution(int $id, int $entryId, ?string $devolutionAmount = null): bool;

    /**
     * Check if an entry is already linked to another amount request
     */
    public function checkIfEntryIsLinked(int $entryId, ?int $excludeRequestId = null): bool;

    /**
     * Update receipt image URL
     */
    public function updateReceiptImageUrl(int $receiptId, string $imageUrl): bool;
}
