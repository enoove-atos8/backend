<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Constants;

class ReminderConstants
{
    // Reminder status
    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_READ = 'read';

    public const STATUS_FAILED = 'failed';

    // Reminder types
    public const TYPE_REQUEST_CREATED = 'request_created';

    public const TYPE_REQUEST_APPROVED = 'request_approved';

    public const TYPE_REQUEST_REJECTED = 'request_rejected';

    public const TYPE_TRANSFER_COMPLETED = 'transfer_completed';

    public const TYPE_PROOF_REMINDER = 'proof_reminder';

    public const TYPE_PROOF_URGENT = 'proof_urgent';

    public const TYPE_PROOF_OVERDUE = 'proof_overdue';

    public const TYPE_PROOF_RECEIVED = 'proof_received';

    public const TYPE_DEVOLUTION_REQUIRED = 'devolution_required';

    public const TYPE_REQUEST_CLOSED = 'request_closed';

    // Table and column names
    public const TABLE = 'amount_request_reminders';

    public const COLUMN_ID = 'id';

    public const COLUMN_AMOUNT_REQUEST_ID = 'amount_request_id';

    public const COLUMN_TYPE = 'type';

    public const COLUMN_STATUS = 'status';

    public const COLUMN_SCHEDULED_AT = 'scheduled_at';

    public const COLUMN_SENT_AT = 'sent_at';

    public const COLUMN_ERROR_MESSAGE = 'error_message';

    public const COLUMN_METADATA = 'metadata';

    public const COLUMN_CREATED_AT = 'created_at';

    public const COLUMN_UPDATED_AT = 'updated_at';
}
