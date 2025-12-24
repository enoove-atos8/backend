<?php

namespace Infrastructure\Repositories\Ecclesiastical\Groups\AmountRequests;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestIndicatorsData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReceiptData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AmountRequestRepository implements AmountRequestRepositoryInterface
{
    // Table names
    const TABLE_NAME = 'amount_requests';

    const RECEIPTS_TABLE_NAME = 'amount_request_receipts';

    const REMINDERS_TABLE_NAME = 'amount_request_reminders';

    const HISTORY_TABLE_NAME = 'amount_request_history';

    const USERS_TABLE_NAME = 'users';

    const USER_DETAILS_TABLE_NAME = 'user_details';

    const MEMBERS_TABLE_NAME = 'members';

    const GROUPS_TABLE_NAME = 'ecclesiastical_divisions_groups';

    const EXITS_TABLE_NAME = 'exits';

    // Pagination
    const PAGINATE_NUMBER = 30;

    // Common column names
    const ID_COLUMN = 'id';

    const DELETED_COLUMN = 'deleted';

    const CREATED_AT_COLUMN = 'created_at';

    const UPDATED_AT_COLUMN = 'updated_at';

    const STATUS_COLUMN = 'status';

    // Amount requests columns
    const MEMBER_ID_COLUMN = 'member_id';

    const GROUP_ID_COLUMN = 'group_id';

    const REQUESTED_AMOUNT_COLUMN = 'requested_amount';

    const DESCRIPTION_COLUMN = 'description';

    const PROOF_DEADLINE_COLUMN = 'proof_deadline';

    const APPROVED_BY_COLUMN = 'approved_by';

    const APPROVED_AT_COLUMN = 'approved_at';

    const REJECTION_REASON_COLUMN = 'rejection_reason';

    const TRANSFER_EXIT_ID_COLUMN = 'transfer_exit_id';

    const TRANSFERRED_AT_COLUMN = 'transferred_at';

    const PROVEN_AMOUNT_COLUMN = 'proven_amount';

    const DEVOLUTION_ENTRY_ID_COLUMN = 'devolution_entry_id';

    const DEVOLUTION_AMOUNT_COLUMN = 'devolution_amount';

    const CLOSED_BY_COLUMN = 'closed_by';

    const CLOSED_AT_COLUMN = 'closed_at';

    const REQUESTED_BY_COLUMN = 'requested_by';

    // Receipts columns
    const AMOUNT_REQUEST_ID_COLUMN = 'amount_request_id';

    const AMOUNT_COLUMN = 'amount';

    const IMAGE_URL_COLUMN = 'image_url';

    const RECEIPT_DATE_COLUMN = 'receipt_date';

    const CREATED_BY_COLUMN = 'created_by';

    // Reminders columns
    const TYPE_COLUMN = 'type';

    const CHANNEL_COLUMN = 'channel';

    const SCHEDULED_AT_COLUMN = 'scheduled_at';

    const SENT_AT_COLUMN = 'sent_at';

    const ERROR_MESSAGE_COLUMN = 'error_message';

    const METADATA_COLUMN = 'metadata';

    // History columns
    const EVENT_COLUMN = 'event';

    const USER_ID_COLUMN = 'user_id';

    // Joined column references
    const TABLE_ID_JOINED = 'amount_requests.id';

    const TABLE_DELETED_JOINED = 'amount_requests.deleted';

    const TABLE_STATUS_JOINED = 'amount_requests.status';

    const TABLE_GROUP_ID_JOINED = 'amount_requests.group_id';

    const TABLE_MEMBER_ID_JOINED = 'amount_requests.member_id';

    const TABLE_CREATED_AT_JOINED = 'amount_requests.created_at';

    const TABLE_PROOF_DEADLINE_JOINED = 'amount_requests.proof_deadline';

    const MEMBERS_ID_JOINED = 'members.id';

    const GROUPS_ID_JOINED = 'ecclesiastical_divisions_groups.id';

    const HISTORY_USER_ID_JOINED = 'amount_request_history.user_id';

    const HISTORY_AMOUNT_REQUEST_ID_JOINED = 'amount_request_history.amount_request_id';

    const HISTORY_CREATED_AT_JOINED = 'amount_request_history.created_at';

    const USERS_ID_JOINED = 'users.id';

    const EXITS_ID_JOINED = 'exits.id';

    const TABLE_TRANSFER_EXIT_ID_JOINED = 'amount_requests.transfer_exit_id';

    const DISPLAY_SELECT_COLUMNS = [
        'amount_requests.id as amount_requests_id',
        'amount_requests.member_id as amount_requests_member_id',
        'amount_requests.group_id as amount_requests_group_id',
        'amount_requests.requested_amount as amount_requests_requested_amount',
        'amount_requests.description as amount_requests_description',
        'amount_requests.proof_deadline as amount_requests_proof_deadline',
        'amount_requests.status as amount_requests_status',
        'amount_requests.approved_by as amount_requests_approved_by',
        'amount_requests.approved_at as amount_requests_approved_at',
        'amount_requests.rejection_reason as amount_requests_rejection_reason',
        'amount_requests.transfer_exit_id as amount_requests_transfer_exit_id',
        'amount_requests.transferred_at as amount_requests_transferred_at',
        'amount_requests.proven_amount as amount_requests_proven_amount',
        'amount_requests.devolution_entry_id as amount_requests_devolution_entry_id',
        'amount_requests.devolution_amount as amount_requests_devolution_amount',
        'amount_requests.closed_by as amount_requests_closed_by',
        'amount_requests.closed_at as amount_requests_closed_at',
        'amount_requests.requested_by as amount_requests_requested_by',
        'amount_requests.created_at as amount_requests_created_at',
        'amount_requests.updated_at as amount_requests_updated_at',
        'amount_requests.deleted as amount_requests_deleted',
    ];

    const MEMBERS_SELECT_COLUMNS = [
        'members.id as members_id',
        'members.full_name as members_full_name',
        'members.avatar as members_avatar',
        'members.email as members_email',
        'members.phone as members_phone',
        'members.cell_phone as members_cell_phone',
    ];

    const GROUPS_SELECT_COLUMNS = [
        'ecclesiastical_divisions_groups.id as groups_id',
        'ecclesiastical_divisions_groups.name as groups_name',
        'ecclesiastical_divisions_groups.slug as groups_slug',
        'ecclesiastical_divisions_groups.ecclesiastical_division_id as groups_division_id',
    ];

    const RECEIPTS_SELECT_COLUMNS = [
        'amount_request_receipts.id as amount_request_receipts_id',
        'amount_request_receipts.amount_request_id as amount_request_receipts_amount_request_id',
        'amount_request_receipts.amount as amount_request_receipts_amount',
        'amount_request_receipts.description as amount_request_receipts_description',
        'amount_request_receipts.image_url as amount_request_receipts_image_url',
        'amount_request_receipts.receipt_date as amount_request_receipts_receipt_date',
        'amount_request_receipts.created_by as amount_request_receipts_created_by',
        'amount_request_receipts.created_at as amount_request_receipts_created_at',
        'amount_request_receipts.updated_at as amount_request_receipts_updated_at',
        'amount_request_receipts.deleted as amount_request_receipts_deleted',
    ];

    const REMINDERS_SELECT_COLUMNS = [
        'amount_request_reminders.id as amount_request_reminders_id',
        'amount_request_reminders.amount_request_id as amount_request_reminders_amount_request_id',
        'amount_request_reminders.type as amount_request_reminders_type',
        'amount_request_reminders.channel as amount_request_reminders_channel',
        'amount_request_reminders.scheduled_at as amount_request_reminders_scheduled_at',
        'amount_request_reminders.sent_at as amount_request_reminders_sent_at',
        'amount_request_reminders.status as amount_request_reminders_status',
        'amount_request_reminders.error_message as amount_request_reminders_error_message',
        'amount_request_reminders.metadata as amount_request_reminders_metadata',
        'amount_request_reminders.created_at as amount_request_reminders_created_at',
        'amount_request_reminders.updated_at as amount_request_reminders_updated_at',
    ];

    const HISTORY_SELECT_COLUMNS = [
        'amount_request_history.id as amount_request_history_id',
        'amount_request_history.amount_request_id as amount_request_history_amount_request_id',
        'amount_request_history.event as amount_request_history_event',
        'amount_request_history.description as amount_request_history_description',
        'amount_request_history.user_id as amount_request_history_user_id',
        'amount_request_history.metadata as amount_request_history_metadata',
        'amount_request_history.created_at as amount_request_history_created_at',
        'amount_request_history.updated_at as amount_request_history_updated_at',
    ];

    const USER_DETAILS_SELECT_COLUMNS = [
        'user_details.full_name as user_details_full_name',
    ];

    const USER_DETAILS_USER_ID_JOINED = 'user_details.user_id';

    const EXITS_SELECT_COLUMNS = [
        'exits.id as exits_id',
        'exits.exit_type as exits_exit_type',
        'exits.amount as exits_amount',
        'exits.transaction_type as exits_transaction_type',
        'exits.date_transaction_compensation as exits_date_transaction_compensation',
        'exits.comments as exits_comments',
        'exits.receipt_link as exits_receipt_link',
    ];

    /**
     * Get all amount requests with optional filters (paginated)
     */
    public function getAll(array $filters = [], bool $paginate = true): Collection|Paginator
    {
        $selectColumns = array_merge(
            self::DISPLAY_SELECT_COLUMNS,
            self::MEMBERS_SELECT_COLUMNS,
            self::GROUPS_SELECT_COLUMNS,
            self::EXITS_SELECT_COLUMNS
        );

        $query = DB::table(self::TABLE_NAME)
            ->select($selectColumns)
            ->leftJoin(
                self::MEMBERS_TABLE_NAME,
                self::TABLE_MEMBER_ID_JOINED,
                '=',
                self::MEMBERS_ID_JOINED
            )
            ->leftJoin(
                self::GROUPS_TABLE_NAME,
                self::TABLE_GROUP_ID_JOINED,
                '=',
                self::GROUPS_ID_JOINED
            )
            ->leftJoin(
                self::EXITS_TABLE_NAME,
                self::TABLE_TRANSFER_EXIT_ID_JOINED,
                '=',
                self::EXITS_ID_JOINED
            )
            ->where(self::TABLE_DELETED_JOINED, false);

        // Apply filters
        if (! empty($filters[self::STATUS_COLUMN])) {
            $query->where(self::TABLE_STATUS_JOINED, $filters[self::STATUS_COLUMN]);
        }

        if (! empty($filters[self::GROUP_ID_COLUMN])) {
            $query->where(self::TABLE_GROUP_ID_JOINED, $filters[self::GROUP_ID_COLUMN]);
        }

        if (! empty($filters[self::MEMBER_ID_COLUMN])) {
            $query->where(self::TABLE_MEMBER_ID_JOINED, $filters[self::MEMBER_ID_COLUMN]);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate(self::TABLE_CREATED_AT_JOINED, '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate(self::TABLE_CREATED_AT_JOINED, '<=', $filters['date_to']);
        }

        $query->orderBy(self::TABLE_CREATED_AT_JOINED, 'desc');

        if ($paginate) {
            $paginator = $query->simplePaginate(self::PAGINATE_NUMBER);

            return $paginator->setCollection(
                $paginator->getCollection()->map(fn ($item) => AmountRequestData::fromResponse((array) $item))
            );
        }

        $results = $query->get();

        return $results->map(fn ($item) => AmountRequestData::fromResponse((array) $item));
    }

    /**
     * Get a single amount request by ID
     */
    public function getById(int $id): ?AmountRequestData
    {
        $selectColumns = array_merge(
            self::DISPLAY_SELECT_COLUMNS,
            self::MEMBERS_SELECT_COLUMNS,
            self::GROUPS_SELECT_COLUMNS,
            self::EXITS_SELECT_COLUMNS
        );

        $result = DB::table(self::TABLE_NAME)
            ->select($selectColumns)
            ->leftJoin(
                self::MEMBERS_TABLE_NAME,
                self::TABLE_MEMBER_ID_JOINED,
                '=',
                self::MEMBERS_ID_JOINED
            )
            ->leftJoin(
                self::GROUPS_TABLE_NAME,
                self::TABLE_GROUP_ID_JOINED,
                '=',
                self::GROUPS_ID_JOINED
            )
            ->leftJoin(
                self::EXITS_TABLE_NAME,
                self::TABLE_TRANSFER_EXIT_ID_JOINED,
                '=',
                self::EXITS_ID_JOINED
            )
            ->where(self::TABLE_ID_JOINED, $id)
            ->where(self::TABLE_DELETED_JOINED, false)
            ->first();

        if ($result === null) {
            return null;
        }

        return AmountRequestData::fromResponse((array) $result);
    }

    /**
     * Create a new amount request
     */
    public function create(AmountRequestData $data): int
    {
        return DB::table(self::TABLE_NAME)->insertGetId([
            self::MEMBER_ID_COLUMN => $data->memberId,
            self::GROUP_ID_COLUMN => $data->groupId,
            self::REQUESTED_AMOUNT_COLUMN => $data->requestedAmount,
            self::DESCRIPTION_COLUMN => $data->description,
            self::PROOF_DEADLINE_COLUMN => $data->proofDeadline,
            self::STATUS_COLUMN => $data->status ?? ReturnMessages::STATUS_PENDING,
            self::REQUESTED_BY_COLUMN => $data->requestedBy,
            self::PROVEN_AMOUNT_COLUMN => '0.00',
            self::DEVOLUTION_AMOUNT_COLUMN => '0.00',
            self::DELETED_COLUMN => false,
            self::CREATED_AT_COLUMN => now(),
            self::UPDATED_AT_COLUMN => now(),
        ]);
    }

    /**
     * Update an existing amount request
     */
    public function update(int $id, AmountRequestData $data): bool
    {
        $updateData = [
            self::UPDATED_AT_COLUMN => now(),
        ];

        if ($data->memberId !== null) {
            $updateData[self::MEMBER_ID_COLUMN] = $data->memberId;
        }

        if ($data->groupId !== null) {
            $updateData[self::GROUP_ID_COLUMN] = $data->groupId;
        }

        if ($data->requestedAmount !== null) {
            $updateData[self::REQUESTED_AMOUNT_COLUMN] = $data->requestedAmount;
        }

        if ($data->description !== null) {
            $updateData[self::DESCRIPTION_COLUMN] = $data->description;
        }

        if ($data->proofDeadline !== null) {
            $updateData[self::PROOF_DEADLINE_COLUMN] = $data->proofDeadline;
        }

        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update($updateData) > 0;
    }

    /**
     * Soft delete an amount request
     */
    public function delete(int $id): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->update([
                self::DELETED_COLUMN => true,
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Approve an amount request
     */
    public function approve(int $id, int $approvedBy): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::STATUS_COLUMN => ReturnMessages::STATUS_APPROVED,
                self::APPROVED_BY_COLUMN => $approvedBy,
                self::APPROVED_AT_COLUMN => now(),
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Reject an amount request
     */
    public function reject(int $id, int $approvedBy, string $rejectionReason): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::STATUS_COLUMN => ReturnMessages::STATUS_REJECTED,
                self::APPROVED_BY_COLUMN => $approvedBy,
                self::APPROVED_AT_COLUMN => now(),
                self::REJECTION_REASON_COLUMN => $rejectionReason,
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Mark an amount request as transferred
     */
    public function markAsTransferred(int $id, int $exitId): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::STATUS_COLUMN => ReturnMessages::STATUS_TRANSFERRED,
                self::TRANSFER_EXIT_ID_COLUMN => $exitId,
                self::TRANSFERRED_AT_COLUMN => now(),
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Link an exit to an amount request (status: approved -> transferred)
     */
    public function linkExit(int $id, int $exitId): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::STATUS_COLUMN => ReturnMessages::STATUS_TRANSFERRED,
                self::TRANSFER_EXIT_ID_COLUMN => $exitId,
                self::TRANSFERRED_AT_COLUMN => now(),
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Unlink an exit from an amount request (status: transferred -> approved)
     */
    public function unlinkExit(int $id): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::STATUS_COLUMN => ReturnMessages::STATUS_APPROVED,
                self::TRANSFER_EXIT_ID_COLUMN => null,
                self::TRANSFERRED_AT_COLUMN => null,
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Get approved amount request by group ID (for auto-linking)
     */
    public function getApprovedByGroupId(int $groupId): ?AmountRequestData
    {
        $selectColumns = array_merge(
            self::DISPLAY_SELECT_COLUMNS,
            self::MEMBERS_SELECT_COLUMNS,
            self::GROUPS_SELECT_COLUMNS
        );

        $result = DB::table(self::TABLE_NAME)
            ->select($selectColumns)
            ->leftJoin(
                self::MEMBERS_TABLE_NAME,
                self::TABLE_MEMBER_ID_JOINED,
                '=',
                self::MEMBERS_ID_JOINED
            )
            ->leftJoin(
                self::GROUPS_TABLE_NAME,
                self::TABLE_GROUP_ID_JOINED,
                '=',
                self::GROUPS_ID_JOINED
            )
            ->where(self::TABLE_GROUP_ID_JOINED, $groupId)
            ->where(self::TABLE_STATUS_JOINED, ReturnMessages::STATUS_APPROVED)
            ->where(self::TABLE_DELETED_JOINED, false)
            ->first();

        if ($result === null) {
            return null;
        }

        return AmountRequestData::fromResponse((array) $result);
    }

    /**
     * Update proven amount and status
     */
    public function updateProvenAmount(int $id, string $provenAmount, string $status): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::PROVEN_AMOUNT_COLUMN => $provenAmount,
                self::STATUS_COLUMN => $status,
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Close an amount request
     */
    public function close(int $id, int $closedBy, ?int $devolutionEntryId, string $devolutionAmount): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::STATUS_COLUMN => ReturnMessages::STATUS_CLOSED,
                self::CLOSED_BY_COLUMN => $closedBy,
                self::CLOSED_AT_COLUMN => now(),
                self::DEVOLUTION_ENTRY_ID_COLUMN => $devolutionEntryId,
                self::DEVOLUTION_AMOUNT_COLUMN => $devolutionAmount,
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Update status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::STATUS_COLUMN => $status,
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Get all receipts for an amount request
     */
    public function getReceipts(int $amountRequestId): Collection
    {
        $results = DB::table(self::RECEIPTS_TABLE_NAME)
            ->select(self::RECEIPTS_SELECT_COLUMNS)
            ->where(self::AMOUNT_REQUEST_ID_COLUMN, $amountRequestId)
            ->where(self::DELETED_COLUMN, false)
            ->orderBy(self::RECEIPT_DATE_COLUMN, 'desc')
            ->get();

        return $results->map(fn ($item) => AmountRequestReceiptData::fromResponse((array) $item));
    }

    /**
     * Create a new receipt
     */
    public function createReceipt(AmountRequestReceiptData $data): int
    {
        return DB::table(self::RECEIPTS_TABLE_NAME)->insertGetId([
            self::AMOUNT_REQUEST_ID_COLUMN => $data->amountRequestId,
            self::AMOUNT_COLUMN => $data->amount,
            self::DESCRIPTION_COLUMN => $data->description,
            self::IMAGE_URL_COLUMN => $data->imageUrl,
            self::RECEIPT_DATE_COLUMN => $data->receiptDate,
            self::CREATED_BY_COLUMN => $data->createdBy,
            self::DELETED_COLUMN => false,
            self::CREATED_AT_COLUMN => now(),
            self::UPDATED_AT_COLUMN => now(),
        ]);
    }

    /**
     * Soft delete a receipt
     */
    public function deleteReceipt(int $amountRequestId, int $receiptId): bool
    {
        return DB::table(self::RECEIPTS_TABLE_NAME)
            ->where(self::ID_COLUMN, $receiptId)
            ->where(self::AMOUNT_REQUEST_ID_COLUMN, $amountRequestId)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::DELETED_COLUMN => true,
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }

    /**
     * Calculate total proven amount from receipts
     */
    public function calculateProvenAmount(int $amountRequestId): string
    {
        $total = DB::table(self::RECEIPTS_TABLE_NAME)
            ->where(self::AMOUNT_REQUEST_ID_COLUMN, $amountRequestId)
            ->where(self::DELETED_COLUMN, false)
            ->sum(self::AMOUNT_COLUMN);

        return number_format((float) $total, 2, '.', '');
    }

    /**
     * Get amount requests with overdue proof deadline
     */
    public function getOverdueRequests(): Collection
    {
        $selectColumns = array_merge(
            self::DISPLAY_SELECT_COLUMNS,
            self::MEMBERS_SELECT_COLUMNS,
            self::GROUPS_SELECT_COLUMNS
        );

        $results = DB::table(self::TABLE_NAME)
            ->select($selectColumns)
            ->leftJoin(
                self::MEMBERS_TABLE_NAME,
                self::TABLE_MEMBER_ID_JOINED,
                '=',
                self::MEMBERS_ID_JOINED
            )
            ->leftJoin(
                self::GROUPS_TABLE_NAME,
                self::TABLE_GROUP_ID_JOINED,
                '=',
                self::GROUPS_ID_JOINED
            )
            ->where(self::TABLE_DELETED_JOINED, false)
            ->whereIn(self::TABLE_STATUS_JOINED, [ReturnMessages::STATUS_TRANSFERRED, ReturnMessages::STATUS_PARTIALLY_PROVEN])
            ->whereDate(self::TABLE_PROOF_DEADLINE_JOINED, '<', now()->format('Y-m-d'))
            ->get();

        return $results->map(fn ($item) => AmountRequestData::fromResponse((array) $item));
    }

    /**
     * Get amount requests approaching deadline
     */
    public function getRequestsApproachingDeadline(int $daysUntilDeadline): Collection
    {
        $selectColumns = array_merge(
            self::DISPLAY_SELECT_COLUMNS,
            self::MEMBERS_SELECT_COLUMNS,
            self::GROUPS_SELECT_COLUMNS
        );

        $targetDate = now()->addDays($daysUntilDeadline)->format('Y-m-d');

        $results = DB::table(self::TABLE_NAME)
            ->select($selectColumns)
            ->leftJoin(
                self::MEMBERS_TABLE_NAME,
                self::TABLE_MEMBER_ID_JOINED,
                '=',
                self::MEMBERS_ID_JOINED
            )
            ->leftJoin(
                self::GROUPS_TABLE_NAME,
                self::TABLE_GROUP_ID_JOINED,
                '=',
                self::GROUPS_ID_JOINED
            )
            ->where(self::TABLE_DELETED_JOINED, false)
            ->whereIn(self::TABLE_STATUS_JOINED, [ReturnMessages::STATUS_TRANSFERRED, ReturnMessages::STATUS_PARTIALLY_PROVEN])
            ->whereDate(self::TABLE_PROOF_DEADLINE_JOINED, '=', $targetDate)
            ->get();

        return $results->map(fn ($item) => AmountRequestData::fromResponse((array) $item));
    }

    /**
     * Get indicators/summary for dashboard
     */
    public function getIndicators(?int $groupId = null): AmountRequestIndicatorsData
    {
        $query = DB::table(self::TABLE_NAME)
            ->where(self::DELETED_COLUMN, false);

        if ($groupId !== null) {
            $query->where(self::GROUP_ID_COLUMN, $groupId);
        }

        $total = (clone $query)->count();
        $totalAmount = (clone $query)->sum(self::REQUESTED_AMOUNT_COLUMN);

        $statusCounts = (clone $query)
            ->selectRaw(self::STATUS_COLUMN.', COUNT(*) as count')
            ->groupBy(self::STATUS_COLUMN)
            ->pluck('count', self::STATUS_COLUMN)
            ->toArray();

        return new AmountRequestIndicatorsData(
            total: $total,
            totalAmount: number_format((float) $totalAmount, 2, '.', ''),
            pending: $statusCounts[ReturnMessages::STATUS_PENDING] ?? 0,
            approved: $statusCounts[ReturnMessages::STATUS_APPROVED] ?? 0,
            rejected: $statusCounts[ReturnMessages::STATUS_REJECTED] ?? 0,
            transferred: $statusCounts[ReturnMessages::STATUS_TRANSFERRED] ?? 0,
            partiallyProven: $statusCounts[ReturnMessages::STATUS_PARTIALLY_PROVEN] ?? 0,
            proven: $statusCounts[ReturnMessages::STATUS_PROVEN] ?? 0,
            overdue: $statusCounts[ReturnMessages::STATUS_OVERDUE] ?? 0,
            closed: $statusCounts[ReturnMessages::STATUS_CLOSED] ?? 0,
        );
    }

    /**
     * Get all reminders for an amount request
     */
    public function getReminders(int $amountRequestId): Collection
    {
        $results = DB::table(self::REMINDERS_TABLE_NAME)
            ->select(self::REMINDERS_SELECT_COLUMNS)
            ->where(self::AMOUNT_REQUEST_ID_COLUMN, $amountRequestId)
            ->orderBy(self::CREATED_AT_COLUMN, 'desc')
            ->get();

        return $results->map(fn ($item) => AmountRequestReminderData::fromResponse((array) $item));
    }

    /**
     * Create a new reminder
     */
    public function createReminder(AmountRequestReminderData $data): int
    {
        return DB::table(self::REMINDERS_TABLE_NAME)->insertGetId([
            self::AMOUNT_REQUEST_ID_COLUMN => $data->amountRequestId,
            self::TYPE_COLUMN => $data->type,
            self::CHANNEL_COLUMN => $data->channel,
            self::SCHEDULED_AT_COLUMN => $data->scheduledAt,
            self::SENT_AT_COLUMN => $data->sentAt,
            self::STATUS_COLUMN => $data->status ?? ReturnMessages::STATUS_PENDING,
            self::ERROR_MESSAGE_COLUMN => $data->errorMessage,
            self::METADATA_COLUMN => $data->metadata ? json_encode($data->metadata) : null,
            self::CREATED_AT_COLUMN => now(),
            self::UPDATED_AT_COLUMN => now(),
        ]);
    }

    /**
     * Get history/timeline for an amount request
     */
    public function getHistory(int $amountRequestId): Collection
    {
        $selectColumns = array_merge(
            self::HISTORY_SELECT_COLUMNS,
            self::USER_DETAILS_SELECT_COLUMNS
        );

        $results = DB::table(self::HISTORY_TABLE_NAME)
            ->select($selectColumns)
            ->leftJoin(
                self::USER_DETAILS_TABLE_NAME,
                self::HISTORY_USER_ID_JOINED,
                '=',
                self::USER_DETAILS_USER_ID_JOINED
            )
            ->where(self::HISTORY_AMOUNT_REQUEST_ID_JOINED, $amountRequestId)
            ->orderBy(self::HISTORY_CREATED_AT_JOINED, 'asc')
            ->get();

        return $results->map(fn ($item) => AmountRequestHistoryData::fromResponse((array) $item));
    }

    /**
     * Create a history record
     */
    public function createHistory(AmountRequestHistoryData $data): int
    {
        return DB::table(self::HISTORY_TABLE_NAME)->insertGetId([
            self::AMOUNT_REQUEST_ID_COLUMN => $data->amountRequestId,
            self::EVENT_COLUMN => $data->event,
            self::DESCRIPTION_COLUMN => $data->description,
            self::USER_ID_COLUMN => $data->userId,
            self::METADATA_COLUMN => $data->metadata ? json_encode($data->metadata) : null,
            self::CREATED_AT_COLUMN => now(),
            self::UPDATED_AT_COLUMN => now(),
        ]);
    }

    /**
     * Update an existing receipt
     */
    public function updateReceipt(int $amountRequestId, int $receiptId, AmountRequestReceiptData $data): bool
    {
        $updateData = [
            self::UPDATED_AT_COLUMN => now(),
        ];

        if ($data->amount !== null) {
            $updateData[self::AMOUNT_COLUMN] = $data->amount;
        }

        if ($data->description !== null) {
            $updateData[self::DESCRIPTION_COLUMN] = $data->description;
        }

        if ($data->imageUrl !== null) {
            $updateData[self::IMAGE_URL_COLUMN] = $data->imageUrl;
        }

        if ($data->receiptDate !== null) {
            $updateData[self::RECEIPT_DATE_COLUMN] = $data->receiptDate;
        }

        return DB::table(self::RECEIPTS_TABLE_NAME)
            ->where(self::ID_COLUMN, $receiptId)
            ->where(self::AMOUNT_REQUEST_ID_COLUMN, $amountRequestId)
            ->where(self::DELETED_COLUMN, false)
            ->update($updateData) > 0;
    }

    /**
     * Get a single receipt by ID
     */
    public function getReceiptById(int $amountRequestId, int $receiptId): ?AmountRequestReceiptData
    {
        $result = DB::table(self::RECEIPTS_TABLE_NAME)
            ->select(self::RECEIPTS_SELECT_COLUMNS)
            ->where(self::ID_COLUMN, $receiptId)
            ->where(self::AMOUNT_REQUEST_ID_COLUMN, $amountRequestId)
            ->where(self::DELETED_COLUMN, false)
            ->first();

        if ($result === null) {
            return null;
        }

        return AmountRequestReceiptData::fromResponse((array) $result);
    }

    /**
     * Get open amount request by group ID (status != closed)
     */
    public function getOpenByGroupId(int $groupId): ?AmountRequestData
    {
        $selectColumns = array_merge(
            self::DISPLAY_SELECT_COLUMNS,
            self::MEMBERS_SELECT_COLUMNS,
            self::GROUPS_SELECT_COLUMNS
        );

        $result = DB::table(self::TABLE_NAME)
            ->select($selectColumns)
            ->leftJoin(
                self::MEMBERS_TABLE_NAME,
                self::TABLE_MEMBER_ID_JOINED,
                '=',
                self::MEMBERS_ID_JOINED
            )
            ->leftJoin(
                self::GROUPS_TABLE_NAME,
                self::TABLE_GROUP_ID_JOINED,
                '=',
                self::GROUPS_ID_JOINED
            )
            ->where(self::TABLE_GROUP_ID_JOINED, $groupId)
            ->where(self::TABLE_STATUS_JOINED, '!=', ReturnMessages::STATUS_CLOSED)
            ->where(self::TABLE_STATUS_JOINED, '!=', ReturnMessages::STATUS_REJECTED)
            ->where(self::TABLE_DELETED_JOINED, false)
            ->first();

        if ($result === null) {
            return null;
        }

        return AmountRequestData::fromResponse((array) $result);
    }

    /**
     * Link a devolution entry to an amount request
     */
    public function linkDevolution(int $id, int $entryId, ?string $devolutionAmount = null): bool
    {
        $updateData = [
            self::DEVOLUTION_ENTRY_ID_COLUMN => $entryId,
            self::UPDATED_AT_COLUMN => now(),
        ];

        if ($devolutionAmount !== null) {
            $updateData[self::DEVOLUTION_AMOUNT_COLUMN] = $devolutionAmount;
        }

        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->where(self::DELETED_COLUMN, false)
            ->update($updateData) > 0;
    }

    /**
     * Update receipt image URL
     */
    public function updateReceiptImageUrl(int $receiptId, string $imageUrl): bool
    {
        return DB::table(self::RECEIPTS_TABLE_NAME)
            ->where(self::ID_COLUMN, $receiptId)
            ->where(self::DELETED_COLUMN, false)
            ->update([
                self::IMAGE_URL_COLUMN => $imageUrl,
                self::UPDATED_AT_COLUMN => now(),
            ]) > 0;
    }
}
