<?php

namespace Domain\Financial\ReceiptProcessing\DataTransferObjects;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use App\Domain\Financial\Reviewers\DataTransferObjects\FinancialReviewerData;
use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\Actions\GetFinancialGroupAction;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\Reviewers\Actions\GetReviewerAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
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

    /** @var FinancialReviewerData | null */
    public ?FinancialReviewerData $reviewer;

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
    public ?string $transactionType;

    /** @var string | null */
    public ?string $transactionCompensation;

    /** @var string | null */
    public ?string $dateTransactionCompensation;


    /** @var string | null */
    public ?string $receiptLink;



    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['receipt_processing_id'] ?? null,
            docType: $data['receipt_processing_doc_type'] ?? null,
            docSubType: $data['receipt_processing_doc_sub_type'] ?? null,
            reviewerId: $data['receipt_processing_reviewer_id'] ?? null,
            amount: $data['receipt_processing_amount'] ?? null,
            reason: $data['receipt_processing_reason'] ?? null,
            status: $data['receipt_processing_status'] ?? null,
            institution: $data['receipt_processing_institution'] ?? null,
            devolution: $data['receipt_processing_devolution'] ?? null,
            isPayment: $data['receipt_processing_is_payment'] ?? null,
            deleted: $data['receipt_processing_deleted'] ?? null,
            transactionType: $data['receipt_processing_transaction_type'] ?? null,
            transactionCompensation: $data['receipt_processing_transaction_compensation'] ?? null,
            dateTransactionCompensation: $data['receipt_processing_date_transaction_compensation'] ?? null,
            receiptLink: $data['receipt_processing_receipt_link'] ?? null,

            reviewer: new FinancialReviewerData([
                'id' => $data['financial_reviewers_id'] ?? null,
                'fullName' => $data['financial_reviewers_full_name'] ?? null,
                'reviewerType' => $data['financial_reviewers_reviewer_type'] ?? null,
                'avatar' => $data['financial_reviewers_avatar'] ?? null,
                'gender' => $data['financial_reviewers_gender'] ?? null,
                'cpf' => $data['financial_reviewers_cpf'] ?? null,
                'rg' => $data['financial_reviewers_rg'] ?? null,
                'email' => $data['financial_reviewers_email'] ?? null,
                'cellPhone' => $data['financial_reviewers_cell_phone'] ?? null,
                'activated' => $data['financial_reviewers_activated'] ?? null,
                'deleted' => $data['financial_reviewers_deleted'] ?? null,
            ]),
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


    /**
     * Create a ReceiptProcessingData instance from SyncStorageData and extracted data
     *
     * @param SyncStorageData $data
     * @param array $extractedData
     * @param string $linkReceipt
     * @param mixed $reviewer
     * @param mixed|null $financialGroup
     * @return self
     * @throws UnknownProperties
     */
    public static function fromExtractedData(
        SyncStorageData $data,
        array $extractedData,
        string $linkReceipt,
        mixed $reviewer,
        mixed $financialGroup = null): self
    {

        $groupReceived = new GroupData(['id' => null]);
        $groupReturned = new GroupData(['id' => null]);
        $isPayment = false;
        $transactionType = '-';

        if($data->docType == EntryRepository::ENTRIES_VALUE)
            $transactionType = EntryRepository::PIX_TRANSACTION_TYPE;

        if($data->isPayment)
            $isPayment = true;

        if ($data->docSubType == EntryRepository::DESIGNATED_VALUE ||
            $data->docSubType == ExitRepository::TRANSFER_VALUE ||
            $data->docSubType == ExitRepository::MINISTERIAL_TRANSFER_VALUE) {

            $groupReceived = new GroupData(['id' => $data->isDevolution ? $financialGroup->id : (int) $data->groupId]);
            $groupReturned = new GroupData(['id' => $data->isDevolution ? (int) $data->groupId : null]);
        }


        return new self([
            'id' => null,
            'docType' => $data->docType,
            'docSubType' => $data->docSubType,
            'reviewer' => new FinancialReviewerData(['id' => $reviewer->id]),
            'division' => new DivisionData(['id' => !is_null($data->divisionId) ? (int) $data->divisionId : null]),
            'groupReceived' => $groupReceived,
            'groupReturned' => $groupReturned,
            'paymentCategory' => new PaymentCategoryData(['id' => !is_null($data->paymentCategoryId) ? (int) $data->paymentCategoryId : null]),
            'paymentItem' => new PaymentItemData(['id' => !is_null($data->paymentItemId) ? (int) $data->paymentItemId : null]),
            'amount' => floatval($extractedData['data']['amount'] ?? 0) / 100,
            'reason' => $extractedData['status'] ?? null,
            'status' => 'error',
            'institution' => isset($extractedData['data']['institution']) && $extractedData['data']['institution'] != ''
                ? $extractedData['data']['institution']
                : null,
            'devolution' => $data->isDevolution == 1,
            'isPayment' => $isPayment,
            'deleted' => false,
            'transactionType' => $transactionType,
            'transactionCompensation' => EntryRepository::COMPENSATED_VALUE,
            'dateTransactionCompensation' => null,
            'receiptLink' => $linkReceipt
        ]);
    }
}
