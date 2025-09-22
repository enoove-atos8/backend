<?php

namespace App\Domain\Financial\Entries\Reports\DataTransferObjects;

use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ReportRequestsData extends DataTransferObject
{
    /** @var integer | null  */
    public int | null $id;

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

    /** @var UserDetailData | null  */
    public ?UserDetailData $userDetail;


    /** @var GroupData | null  */
    public ?GroupData $group;



    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['reports_id'] ?? null,
            reportName: $data['reports_report_name'] ?? null,
            detailedReport: $data['reports_detailed_report'] ?? null,
            generationDate: $data['reports_generation_date'] ?? null,
            dates: json_decode($data['reports_dates'], true) ?? null,
            status: $data['reports_status'] ?? null,
            error: $data['reports_error'] ?? null,
            startedBy: $data['reports_started_by'] ?? null,
            entryTypes: json_decode($data['reports_entry_types'], true) ?? null,
            groupReceivedId: $data['reports_group_received_id'] ?? null,
            dateOrder: $data['reports_date_order'] ?? false,
            allGroupsReceipts: $data['reports_all_groups_receipts'] ?? false,
            includeCashDeposit: $data['reports_include_cash_deposit'] ?? false,
            linkReport: $data['reports_link_report'] ?? false,
            designatedAmount: $data['reports_designated_amount'] ?? false,
            offerAmount: $data['reports_offer_amount'] ?? false,
            titheAmount: $data['reports_tithe_amount'] ?? false,

            userDetail: new UserDetailData([
                'id' => $data['user_detail_user_id'] ?? null,
                'name' => $data['user_detail_full_name'] ?? null,
                'avatar' => $data['user_detail_avatar'] ?? null,
            ]),

            group: new GroupData([
                'id' => $data['groups_id'] ?? null,
                'name' => $data['groups_name'] ?? null,
            ]),
        );
    }

}
