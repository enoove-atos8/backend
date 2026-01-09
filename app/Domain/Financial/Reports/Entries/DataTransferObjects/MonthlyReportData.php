<?php

namespace App\Domain\Financial\Reports\Entries\DataTransferObjects;

use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Ecclesiastical\Groups\Groups\DataTransferObjects\GroupData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonthlyReportData extends DataTransferObject
{
    /** @var integer | null  */
    public int | null $id;

    /** @var integer | null  */
    public int | null $accountId;

    /** @var string | null  */
    public string | null $reportName;

    /** @var boolean | null  */
    public bool | null $detailedReport;

    /** @var string | null  */
    public string | null $generationDate;

    /** @var array | null  */
    public array | null $dates;

    /** @var string|null  */
    public string | null $status;

    /** @var string|null  */
    public string | null $error;

    /** @var string|null  */
    public string | null $linkReport;

    /** @var integer | null  */
    public int | null $startedBy;

    /** @var array | null  */
    public array | null $entryTypes;

    /** @var integer | null  */
    public int | null $groupReceivedId;

    /** @var boolean | null  */
    public bool | null $dateOrder;

    /** @var boolean | null  */
    public bool | null $allGroupsReceipts;

    /** @var boolean | null  */
    public bool | null $includeCashDeposit;

    /** @var string|null  */
    public string | null $designatedAmount;


    /** @var string|null  */
    public string | null $offerAmount;


    /** @var string|null  */
    public string | null $titheAmount;

    /** @var boolean|null  */
    public bool | null $includeGroupsEntries;


    /** @var boolean|null  */
    public bool | null $includeAnonymousOffers;

    /** @var boolean|null  */
    public bool | null $includeTransfersBetweenAccounts;

    /** @var string|null  */
    public string | null $monthlyEntriesAmount;

    /** @var UserDetailData | null  */
    public ?UserDetailData $userDetail;


    /** @var GroupData | null  */
    public ?GroupData $group;

    /** @var AccountData | null  */
    public ?AccountData $account;



    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['reports_id'] ?? null,
            accountId: $data['reports_account_id'] ?? null,
            groupReceivedId: $data['group_received_id'] ?? null,
            reportName: $data['reports_report_name'] ?? null,
            detailedReport: $data['reports_detailed_report'] ?? false,
            generationDate: $data['reports_generation_date'] ?? null,
            dates: json_decode($data['reports_dates'], true) ?? null,
            status: $data['reports_status'] ?? null,
            error: $data['reports_error'] ?? null,
            startedBy: $data['reports_started_by'] ?? null,
            entryTypes: json_decode($data['reports_entry_types'], true) ?? null,
            dateOrder: $data['reports_date_order'] ?? null,
            allGroupsReceipts: $data['reports_all_groups_receipts'] ?? false,
            includeCashDeposit: $data['reports_include_cash_deposit'] ?? false,
            linkReport: $data['reports_link_report'] ?? null,
            designatedAmount: $data['reports_designated_amount'] ?? null,
            offerAmount: $data['reports_offer_amount'] ?? null,
            titheAmount: $data['reports_tithe_amount'] ?? null,
            includeGroupsEntries: $data['reports_include_groups_entries'] ?? false,
            includeAnonymousOffers: $data['reports_include_anonymous_offers'] ?? false,
            includeTransfersBetweenAccounts: $data['reports_include_transfers_between_accounts'] ?? false,
            monthlyEntriesAmount: $data['reports_monthly_entries_amount'] ?? null,

            userDetail: new UserDetailData([
                'id' => $data['user_detail_user_id'] ?? null,
                'name' => $data['user_detail_full_name'] ?? null,
                'avatar' => $data['user_detail_avatar'] ?? null,
            ]),

            group: new GroupData([
                'id' => $data['groups_id'] ?? null,
                'name' => $data['groups_name'] ?? null,
            ]),

            account: new AccountData([
                'id' => $data['accounts_id'] ?? null,
                'accountType' => $data['accounts_account_type'] ?? null,
                'bankName' => $data['accounts_bank_name'] ?? null,
                'agencyNumber' => $data['accounts_agency_number'] ?? null,
                'accountNumber' => $data['accounts_account_number'] ?? null,
            ]),
        );
    }
}
