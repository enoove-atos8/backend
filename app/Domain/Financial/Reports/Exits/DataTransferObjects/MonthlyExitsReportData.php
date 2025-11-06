<?php

namespace App\Domain\Financial\Reports\Exits\DataTransferObjects;

use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonthlyExitsReportData extends DataTransferObject
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
    public array | null $exitTypes;

    /** @var boolean | null  */
    public bool | null $dateOrder;

    /** @var float | null  */
    public float | null $amount;

    /** @var UserDetailData | null  */
    public ?UserDetailData $userDetail;

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
            reportName: $data['reports_report_name'] ?? null,
            detailedReport: $data['reports_detailed_report'] ?? false,
            generationDate: $data['reports_generation_date'] ?? null,
            dates: json_decode($data['reports_dates'], true) ?? null,
            status: $data['reports_status'] ?? null,
            error: $data['reports_error'] ?? null,
            startedBy: $data['reports_started_by'] ?? null,
            exitTypes: json_decode($data['reports_exit_types'], true) ?? null,
            dateOrder: $data['reports_date_order'] ?? null,
            linkReport: $data['reports_link_report'] ?? null,
            amount: isset($data['reports_amount']) ? (float) $data['reports_amount'] : null,

            userDetail: new UserDetailData([
                'id' => $data['user_detail_user_id'] ?? null,
                'name' => $data['user_detail_full_name'] ?? null,
                'avatar' => $data['user_detail_avatar'] ?? null,
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
