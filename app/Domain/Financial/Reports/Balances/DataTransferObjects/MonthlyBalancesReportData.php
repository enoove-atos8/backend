<?php

namespace App\Domain\Financial\Reports\Balances\DataTransferObjects;

use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonthlyBalancesReportData extends DataTransferObject
{
    public ?int $id;

    public ?int $accountId;

    public ?string $reportName;

    public ?string $generationDate;

    public ?array $dates;

    public ?string $status;

    public ?string $error;

    public ?string $linkReport;

    public ?int $startedBy;

    public ?UserDetailData $userDetail;

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
            generationDate: $data['reports_generation_date'] ?? null,
            dates: json_decode($data['reports_dates'], true) ?? null,
            status: $data['reports_status'] ?? null,
            error: $data['reports_error'] ?? null,
            startedBy: $data['reports_started_by'] ?? null,
            linkReport: $data['reports_link_report'] ?? null,

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
