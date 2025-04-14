<?php

namespace Domain\Financial\ReceiptProcessing\DataTransferObjects;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ReceiptProcessingData extends DataTransferObject
{
    /** @var integer | null */
    public ?int $id;

    /** @var string | null */
    public ?string $docType;

    /** @var string | null */
    public ?string $docSubType;

    /** @var DivisionData | null */
    public ?DivisionData $division;

    /** @var GroupData | null */
    public ?GroupData $groupReturned;

    /** @var GroupData | null */
    public ?GroupData $groupReceived;

    /** @var PaymentCategoryData | null */
    public ?PaymentCategoryData $paymentCategory;

    /** @var PaymentItemData | null */
    public ?PaymentItemData $paymentItem;

    /** @var string | null */
    public ?string $amount;

    /** @var string | null */
    public ?string $reason;

    /** @var string | null */
    public ?string $status;

    /** @var string | null */
    public ?string $institution;

    /** @var boolean | null */
    public ?bool $devolution;

    /** @var boolean | null */
    public ?bool $isPayment;

    /** @var boolean | null */
    public ?bool $deleted;

    /** @var string | null */
    public ?string $receiptLink;



    /**
     * @throws UnknownProperties
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['receipt_processing_id'] ?? null,
            docType: $data['receipt_processing_doc_type'] ?? null,
            docSubType: $data['receipt_processing_doc_sub_type'] ?? null,
            amount: $data['receipt_processing_amount'] ?? null,
            reason: $data['receipt_processing_reason'] ?? null,
            status: $data['receipt_processing_status'] ?? null,
            institution: $data['receipt_processing_institution'] ?? null,
            devolution: $data['receipt_processing_devolution'] ?? null,
            isPayment: $data['receipt_processing_is_payment'] ?? null,
            deleted: $data['receipt_processing_deleted'] ?? null,
            receiptLink: $data['receipt_processing_receipt_link'] ?? null,

            division: new DivisionData([
                'id' => $data['division_id'] ?? null,
                'slug' => $data['division_slug'] ?? null,
                'name' => $data['division_name'] ?? null,
                'description' => $data['division_description'] ?? null,
                'enabled' => $data['division_enabled'] ?? null,
            ]),
            groupReturned: new GroupData([
                'id' => $data['g_returned_id'] ?? null,
                'divisionId' => $data['g_returned_division_id'] ?? null,
                'parentGroupId' => $data['g_returned_parent_group_id'] ?? null,
                'leaderId' => $data['g_returned_leader_id'] ?? null,
                'name' => $data['g_returned_name'] ?? null,
                'description' => $data['g_returned_description'] ?? null,
                'slug' => $data['g_returned_slug'] ?? null,
                'enabled' => $data['g_returned_enabled'] ?? null,
                'temporaryEvent' => $data['g_returned_temporary_event'] ?? null,
                'returnValues' => $data['g_returned_return_values'] ?? null,
                'financialGroup' => $data['g_returned_financial_group'] ?? null,
                'startDate' => $data['g_returned_start_date'] ?? null,
                'endDate' => $data['g_returned_end_date'] ?? null,
            ]),
            groupReceived: new GroupData([
                'id' => $data['g_received_id'] ?? null,
                'divisionId' => $data['g_received_division_id'] ?? null,
                'parentGroupId' => $data['g_received_parent_group_id'] ?? null,
                'leaderId' => $data['g_received_leader_id'] ?? null,
                'name' => $data['g_received_name'] ?? null,
                'description' => $data['g_received_description'] ?? null,
                'slug' => $data['g_received_slug'] ?? null,
                'enabled' => $data['g_received_enabled'] ?? null,
                'temporaryEvent' => $data['g_received_temporary_event'] ?? null,
                'returnValues' => $data['g_received_return_values'] ?? null,
                'financialGroup' => $data['g_received_financial_group'] ?? null,
                'startDate' => $data['g_received_start_date'] ?? null,
                'endDate' => $data['g_received_end_date'] ?? null,
            ]),
            paymentCategory: new PaymentCategoryData([
                'id' => $data['payment_category_id'] ?? null,
                'slug' => $data['payment_category_slug'] ?? null,
                'name' => $data['payment_category_name'] ?? null,
                'description' => $data['payment_category_description'] ?? null,
            ]),
            paymentItem: new PaymentItemData([
                'id' => $data['payment_item_id'] ?? null,
                'paymentCategoryId' => $data['payment_item_payment_category_id'] ?? null,
                'slug' => $data['payment_item_slug'] ?? null,
                'name' => $data['payment_item_name'] ?? null,
                'description' => $data['payment_item_description'] ?? null,
            ])
        );
    }
}
