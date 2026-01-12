<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects;

use App\Domain\Ecclesiastical\Groups\Groups\DataTransferObjects\GroupData;
use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AmountRequestData extends DataTransferObject
{
    public ?int $id;

    public ?int $memberId;

    public ?int $groupId;

    public ?string $requestedAmount;

    public ?string $description;

    public ?string $proofDeadline;

    public ?string $type;

    public ?bool $aboveLimit;

    public ?string $status;

    public ?int $approvedBy;

    public ?string $approvedAt;

    public ?string $rejectionReason;

    public ?int $transferExitId;

    public ?string $transferredAt;

    public ?string $provenAmount;

    public ?int $devolutionEntryId;

    public ?string $devolutionAmount;

    public ?int $closedBy;

    public ?string $closedAt;

    public ?int $requestedBy;

    public ?string $createdAt;

    public ?string $updatedAt;

    public ?bool $deleted;

    public ?MemberData $member;

    public ?GroupData $group;

    /** @var array|null Transfer exit data */
    public ?array $transferExit;

    /** @var AmountRequestReceiptData[]|null */
    public ?array $receipts;

    /**
     * Create an AmountRequestData instance from a database response array.
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['amount_requests_id'] ?? null,
            memberId: $data['amount_requests_member_id'] ?? null,
            groupId: $data['amount_requests_group_id'] ?? null,
            requestedAmount: $data['amount_requests_requested_amount'] ?? null,
            description: $data['amount_requests_description'] ?? null,
            proofDeadline: $data['amount_requests_proof_deadline'] ?? null,
            type: $data['amount_requests_type'] ?? 'group_fund',
            aboveLimit: isset($data['amount_requests_above_limit']) ? (bool) $data['amount_requests_above_limit'] : false,
            status: $data['amount_requests_status'] ?? null,
            approvedBy: $data['amount_requests_approved_by'] ?? null,
            approvedAt: $data['amount_requests_approved_at'] ?? null,
            rejectionReason: $data['amount_requests_rejection_reason'] ?? null,
            transferExitId: $data['amount_requests_transfer_exit_id'] ?? null,
            transferredAt: $data['amount_requests_transferred_at'] ?? null,
            provenAmount: $data['amount_requests_proven_amount'] ?? null,
            devolutionEntryId: $data['amount_requests_devolution_entry_id'] ?? null,
            devolutionAmount: $data['amount_requests_devolution_amount'] ?? null,
            closedBy: $data['amount_requests_closed_by'] ?? null,
            closedAt: $data['amount_requests_closed_at'] ?? null,
            requestedBy: $data['amount_requests_requested_by'] ?? null,
            createdAt: $data['amount_requests_created_at'] ?? null,
            updatedAt: $data['amount_requests_updated_at'] ?? null,
            deleted: isset($data['amount_requests_deleted']) ? (bool) $data['amount_requests_deleted'] : null,
            member: isset($data['members_id']) ? MemberData::fromResponse($data) : null,
            group: isset($data['groups_id']) ? GroupData::fromResponse($data) : null,
            transferExit: isset($data['exits_id']) && $data['exits_id'] !== null ? [
                'id' => $data['exits_id'],
                'exitType' => $data['exits_exit_type'] ?? null,
                'amount' => $data['exits_amount'] ?? null,
                'transactionType' => $data['exits_transaction_type'] ?? null,
                'dateTransactionCompensation' => $data['exits_date_transaction_compensation'] ?? null,
                'comments' => $data['exits_comments'] ?? null,
                'receiptLink' => $data['exits_receipt_link'] ?? null,
            ] : null,
        );
    }

    /**
     * Create an AmountRequestData instance from an Eloquent model array.
     *
     * @throws UnknownProperties
     */
    public static function fromSelf(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'memberId' => $data['member_id'] ?? null,
            'groupId' => $data['group_id'] ?? null,
            'requestedAmount' => $data['requested_amount'] ?? null,
            'description' => $data['description'] ?? null,
            'proofDeadline' => $data['proof_deadline'] ?? null,
            'type' => $data['type'] ?? 'group_fund',
            'aboveLimit' => isset($data['above_limit']) ? (bool) $data['above_limit'] : false,
            'status' => $data['status'] ?? null,
            'approvedBy' => $data['approved_by'] ?? null,
            'approvedAt' => $data['approved_at'] ?? null,
            'rejectionReason' => $data['rejection_reason'] ?? null,
            'transferExitId' => $data['transfer_exit_id'] ?? null,
            'transferredAt' => $data['transferred_at'] ?? null,
            'provenAmount' => $data['proven_amount'] ?? null,
            'devolutionEntryId' => $data['devolution_entry_id'] ?? null,
            'devolutionAmount' => $data['devolution_amount'] ?? null,
            'closedBy' => $data['closed_by'] ?? null,
            'closedAt' => $data['closed_at'] ?? null,
            'requestedBy' => $data['requested_by'] ?? null,
            'createdAt' => $data['created_at'] ?? null,
            'updatedAt' => $data['updated_at'] ?? null,
            'deleted' => $data['deleted'] ?? null,
        ]);
    }
}
