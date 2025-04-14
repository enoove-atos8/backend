<?php

namespace Domain\Financial\Exits\Exits\DataTransferObjects;

use App\Domain\Financial\Exits\Payments\PaymentCategory\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\PaymentItem\DataTransferObjects\PaymentItemData;
use App\Domain\Financial\Reviewers\DataTransferObjects\FinancialReviewerData;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ExitData extends DataTransferObject
{
    /** @var integer | null */
    public ?int $id;

    /** @var string | null */
    public ?string $exitType;

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
    public ?string $dateExitRegister;

    /** @var string | null */
    public ?string $timestampExitTransaction;

    /** @var string | null */
    public ?string $amount;

    /** @var string | null */
    public ?string $comments;

    /** @var string | null */
    public ?string $receiptLink;

    /** @var FinancialReviewerData | null */
    public ?FinancialReviewerData $financialReviewer;

    /** @var DivisionData | null */
    public ?DivisionData $division;

    /** @var GroupData | null */
    public ?GroupData $group;

    /** @var \App\Domain\Financial\Exits\Payments\PaymentCategory\DataTransferObjects\PaymentCategoryData | null */
    public ?\App\Domain\Financial\Exits\Payments\PaymentCategory\DataTransferObjects\PaymentCategoryData $paymentCategory;

    /** @var PaymentItemData | null */
    public ?PaymentItemData $paymentItem;


    /**
     * @throws UnknownProperties
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['exits_id'] ?? null,
            reviewerId: $data['exits_reviewer_id'] ?? null,
            exitType: $data['exits_exit_type'] ?? null,
            isPayment: $data['exits_is_payment'] ?? null,
            deleted: $data['exits_deleted'] ?? null,
            transactionType: $data['exits_transaction_type'] ?? null,
            transactionCompensation: $data['exits_transaction_compensation'] ?? null,
            dateTransactionCompensation: $data['exits_date_transaction_compensation'] ?? null,
            dateExitRegister: $data['exits_date_exit_register'] ?? null,
            timestampExitTransaction: $data['exits_timestamp_exit_transaction'] ?? null,
            amount: $data['exits_amount'] ?? null,
            comments: $data['exits_comments'] ?? null,
            receiptLink: $data['exits_receipt_link'] ?? null,

            financialReviewer: new FinancialReviewerData([
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
                'rememberToken' => $data['financial_reviewers_remember_token'] ?? null,
            ]),

            division: new DivisionData([
                'id' => $data['division_id'] ?? null,
                'slug' => $data['division_slug'] ?? null,
                'name' => $data['division_name'] ?? null,
                'description' => $data['division_description'] ?? null,
                'enabled' => $data['division_enabled'] ?? null,
            ]),

            group: new GroupData([
                'id' => $data['groups_id'] ?? null,
                'divisionId' => $data['groups_division_id'] ?? null,
                'parentGroupId' => $data['groups_parent_group_id'] ?? null,
                'leaderId' => $data['groups_leader_id'] ?? null,
                'name' => $data['groups_name'] ?? null,
                'description' => $data['groups_description'] ?? null,
                'slug' => $data['groups_slug'] ?? null,
                'transactionsExists' => $data['groups_transactions_exists'] ?? null,
                'enabled' => $data['groups_enabled'] ?? null,
                'temporaryEvent' => $data['groups_temporary_event'] ?? null,
                'returnValues' => $data['groups_return_values'] ?? null,
                'financialGroup' => $data['groups_financial_group'] ?? null,
                'startDate' => $data['groups_start_date'] ?? null,
                'endDate' => $data['groups_end_date'] ?? null,
                'updatedAt' => $data['groups_updated_at'] ?? null,
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
