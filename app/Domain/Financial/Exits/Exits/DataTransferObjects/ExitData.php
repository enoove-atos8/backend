<?php

namespace Domain\Financial\Exits\Exits\DataTransferObjects;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use App\Domain\Financial\Reviewers\DataTransferObjects\FinancialReviewerData;
use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use DateTime;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Exception;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ExitData extends DataTransferObject
{
    /** @var integer | null */
    public ?int $id;

    /** @var integer | null */
    public ?int $accountId;

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

    /** @var PaymentCategoryData | null */
    public ?PaymentCategoryData $paymentCategory;

    /** @var PaymentItemData | null */
    public ?PaymentItemData $paymentItem;

    /** @var AccountData | null */
    public ?AccountData $account;


    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['exits_id'] ?? null,
            accountId: $data['exits_account_id'] ?? null,
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
            ]),

            account: new AccountData([
                'id' => $data['accounts_id'] ?? null,
                'accountType' => $data['accounts_account_type'] ?? null,
                'bankName' => $data['accounts_bank_name'] ?? null,
                'agencyNumber' => $data['accounts_agency_number'] ?? null,
                'accountNumber' => $data['accounts_account_number'] ?? null,
                'activated' => $data['accounts_activated'] ?? null,
            ])
        );
    }



    /**
     * Create an ExitData instance from extracted data
     *
     * @param array $extractedData Data extracted from receipt
     * @param SyncStorageData $data Sync storage data
     * @param object $reviewer Reviewer object
     * @param string|null $nextBusinessDay Function to get next business day from a date
     * @return self New ExitData instance
     * @throws UnknownProperties
     * @throws Exception
     */
    public static function fromExtractedData(
        array $extractedData,
        SyncStorageData $data,
        object $reviewer,
        ?string $nextBusinessDay = null
    ): self {
        $currentDate = date('Y-m-d');
        $extractedDate = $extractedData['data']['date'];

        $dateTransactionCompensation = $nextBusinessDay ?
            $nextBusinessDay . 'T03:00:00.000Z' :
            (new DateTime($extractedDate))->format('Y-m-d') . 'T03:00:00.000Z';

        $instance = new self([
            'id' => null,
            'accountId' => $data->accountId,
            'amount' => floatval($extractedData['data']['amount']) / 100,
            'comments' => 'Saída registrada automaticamente!',
            'dateExitRegister' => $currentDate,
            'dateTransactionCompensation' => $dateTransactionCompensation,
            'deleted' => 0,
            'exitType' => $data->docSubType,
            'receiptLink' => '',
            'timestampExitTransaction' => null,
            'transactionCompensation' => ExitRepository::COMPENSATED_VALUE,
            'transactionType' => ExitRepository::PIX_VALUE,
            'isPayment' => 0,
            'financialReviewer' => new FinancialReviewerData(['id' => $reviewer->id]),
            'division' => new DivisionData(['id' => null]),
            'group' => new GroupData(['id' => null]),
            'paymentCategory' => new PaymentCategoryData(['id' => null]),
            'paymentItem' => new PaymentItemData(['id' => null]),
            'account' => new AccountData(['id' => null])
        ]);

        if ($data->docSubType == ExitRepository::PAYMENTS_VALUE)
        {
            $instance->isPayment = 1;
            $instance->paymentItem = new PaymentItemData(['id' => $data->paymentItemId]);
            $instance->paymentCategory = new PaymentCategoryData(['id' => $data->paymentCategoryId]);
        }

        if ($data->docSubType != ExitRepository::PAYMENTS_VALUE)
        {
            $instance->group = new GroupData(['id' => $data->groupId]);
            $instance->division = new DivisionData(['id' => $data->divisionId]);
        }

        if ($data->docSubType == ExitRepository::ACCOUNTS_TRANSFER_VALUE)
        {
            $instance->exitType = ExitRepository::ACCOUNTS_TRANSFER_VALUE;
            $instance->group = new GroupData(['id' => null]);
            $instance->division = new DivisionData(['id' => null]);
            $instance->isPayment = 0;
        }

        return $instance;
    }
}
