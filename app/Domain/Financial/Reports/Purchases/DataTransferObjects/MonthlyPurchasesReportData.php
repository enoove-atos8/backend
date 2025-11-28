<?php

namespace App\Domain\Financial\Reports\Purchases\DataTransferObjects;

use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Domain\Financial\AccountsAndCards\Cards\DataTransferObjects\CardData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonthlyPurchasesReportData extends DataTransferObject
{
    /** @var integer | null  */
    public int | null $id;

    /** @var integer | null  */
    public int | null $cardId;

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

    /** @var boolean | null  */
    public bool | null $dateOrder;

    /** @var boolean | null  */
    public bool | null $allCardsReceipts;

    /** @var float | null  */
    public float | null $amount;

    /** @var UserDetailData | null  */
    public ?UserDetailData $userDetail;

    /** @var CardData | null  */
    public ?CardData $card;


    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['reports_id'] ?? null,
            cardId: $data['reports_card_id'] ?? null,
            reportName: $data['reports_report_name'] ?? null,
            detailedReport: $data['reports_detailed_report'] ?? false,
            generationDate: $data['reports_generation_date'] ?? null,
            dates: json_decode($data['reports_dates'], true) ?? null,
            status: $data['reports_status'] ?? null,
            error: $data['reports_error'] ?? null,
            startedBy: $data['reports_started_by'] ?? null,
            dateOrder: $data['reports_date_order'] ?? null,
            allCardsReceipts: $data['reports_all_cards_receipts'] ?? false,
            linkReport: $data['reports_link_report'] ?? null,
            amount: isset($data['reports_amount']) ? (float) $data['reports_amount'] : null,

            userDetail: new UserDetailData([
                'id' => $data['user_detail_user_id'] ?? null,
                'name' => $data['user_detail_full_name'] ?? null,
                'avatar' => $data['user_detail_avatar'] ?? null,
            ]),

            card: isset($data['cards_id']) && $data['cards_id'] ? new CardData([
                'id' => $data['cards_id'] ?? null,
                'name' => $data['cards_name'] ?? null,
                'description' => null,
                'cardNumber' => $data['cards_card_number'] ?? null,
                'expiryDate' => null,
                'dueDay' => null,
                'closingDay' => null,
                'status' => null,
                'active' => null,
                'deleted' => null,
                'creditCardBrand' => $data['cards_credit_card_brand'] ?? null,
                'personType' => null,
                'cardHolderName' => null,
                'limit' => null,
            ]) : null,
        );
    }
}
