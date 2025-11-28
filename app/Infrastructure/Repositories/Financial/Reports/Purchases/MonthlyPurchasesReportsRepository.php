<?php

namespace App\Infrastructure\Repositories\Financial\Reports\Purchases;

use App\Domain\Financial\Reports\Purchases\DataTransferObjects\MonthlyPurchasesReportData;
use App\Domain\Financial\Reports\Purchases\Interfaces\MonthlyPurchasesReportsRepositoryInterface;
use App\Domain\Financial\Reports\Purchases\Models\PurchasesReportRequests;
use App\Infrastructure\Repositories\Users\User\UserDetailRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class MonthlyPurchasesReportsRepository extends BaseRepository implements MonthlyPurchasesReportsRepositoryInterface
{

    protected mixed $model = PurchasesReportRequests::class;

    const TABLE_NAME = 'purchases_reports_request';
    const STATUS_COLUMN = 'status';
    const ID_JOINED = 'purchases_reports_request.id';
    const DATES_COLUMN = 'dates';
    const TO_PROCESS_STATUS_VALUE = 'to_process';
    const IN_PROGRESS_STATUS_VALUE = 'in_progress';
    const DONE_STATUS_VALUE = 'done';
    const ERROR_STATUS_VALUE = 'error';
    const NO_DATA_STATUS_VALUE = 'no_data';
    const MONTHLY_PURCHASES_REPORT_NAME = 'monthly_purchases';
    const MONTHLY_RECEIPTS_PURCHASE_REPORT_NAME = 'monthly_receipts_purchase';
    const CARD_ID_JOINED = 'purchases_reports_request.card_id';
    const START_BY_COLUMN = 'purchases_reports_request.started_by';

    const CARDS_TABLE_NAME = 'cards';
    const CARDS_ID_COLUMN_JOINED = 'cards.id';

    const PAGINATE_NUMBER = 30;


    const DISPLAY_SELECT_COLUMNS = [
        'purchases_reports_request.id as reports_id',
        'purchases_reports_request.card_id as reports_card_id',
        'purchases_reports_request.started_by as reports_started_by',
        'purchases_reports_request.report_name as reports_report_name',
        'purchases_reports_request.detailed_report as reports_detailed_report',
        'purchases_reports_request.generation_date as reports_generation_date',
        'purchases_reports_request.dates as reports_dates',
        'purchases_reports_request.status as reports_status',
        'purchases_reports_request.error as reports_error',
        'purchases_reports_request.date_order as reports_date_order',
        'purchases_reports_request.all_cards_receipts as reports_all_cards_receipts',
        'purchases_reports_request.link_report as reports_link_report',
        'purchases_reports_request.amount as reports_amount',
    ];

    const CARDS_DISPLAY_SELECT_COLUMNS = [
        'cards.id as cards_id',
        'cards.name as cards_name',
        'cards.card_number as cards_card_number',
        'cards.credit_card_brand as cards_credit_card_brand',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param MonthlyPurchasesReportData $monthlyPurchasesReportData
     * @return PurchasesReportRequests
     */
    public function generateMonthlyPurchasesReport(MonthlyPurchasesReportData $monthlyPurchasesReportData): PurchasesReportRequests
    {
        return $this->create([
            'card_id'                               => $monthlyPurchasesReportData->cardId,
            'started_by'                            => $monthlyPurchasesReportData->startedBy,
            'report_name'                           => $monthlyPurchasesReportData->reportName,
            'detailed_report'                       => $monthlyPurchasesReportData->detailedReport,
            'generation_date'                       => $monthlyPurchasesReportData->generationDate,
            'dates'                                 => $monthlyPurchasesReportData->dates,
            'status'                                => $monthlyPurchasesReportData->status,
            'error'                                 => $monthlyPurchasesReportData->error,
            'date_order'                            => $monthlyPurchasesReportData->dateOrder,
            'all_cards_receipts'                    => $monthlyPurchasesReportData->allCardsReceipts,
        ]);
    }


    /**
     * @param MonthlyPurchasesReportData $monthlyPurchasesReportData
     * @return PurchasesReportRequests
     */
    public function generateMonthlyReceiptsPurchaseReport(MonthlyPurchasesReportData $monthlyPurchasesReportData): PurchasesReportRequests
    {
        return $this->create([
            'card_id'                               => $monthlyPurchasesReportData->cardId,
            'started_by'                            => $monthlyPurchasesReportData->startedBy,
            'report_name'                           => $monthlyPurchasesReportData->reportName,
            'detailed_report'                       => $monthlyPurchasesReportData->detailedReport,
            'generation_date'                       => $monthlyPurchasesReportData->generationDate,
            'dates'                                 => $monthlyPurchasesReportData->dates,
            'status'                                => $monthlyPurchasesReportData->status,
            'error'                                 => $monthlyPurchasesReportData->error,
            'date_order'                            => $monthlyPurchasesReportData->dateOrder,
            'all_cards_receipts'                    => $monthlyPurchasesReportData->allCardsReceipts,
        ]);
    }


    /**
     * @param bool $paginate
     * @return Collection|Paginator
     * @throws BindingResolutionException
     */
    public function getReports(bool $paginate = true): Collection | Paginator
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            UserDetailRepository::DISPLAY_SELECT_COLUMNS,
            self::CARDS_DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use (
            $paginate, $displayColumnsFromRelationship) {

            $q = DB::table(MonthlyPurchasesReportsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    MonthlyPurchasesReportsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->leftJoin(
                    MonthlyPurchasesReportsRepository::CARDS_TABLE_NAME,
                    MonthlyPurchasesReportsRepository::CARD_ID_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    MonthlyPurchasesReportsRepository::CARDS_ID_COLUMN_JOINED)
                ->orderByDesc(MonthlyPurchasesReportsRepository::ID_JOINED);


            if (!$paginate)
            {
                $result = $q->get();
                return collect($result)->map(fn($item) => MonthlyPurchasesReportData::fromResponse((array) $item));
            }
            else
            {
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $result->setCollection(
                    $result->getCollection()->map(fn($item) => MonthlyPurchasesReportData::fromResponse((array) $item))
                );

                return $result;
            }
        };

        return $this->doQuery($query);
    }


    /**
     * @param string $status
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getReportsByStatus(string $status): Collection
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            UserDetailRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use ($status, $displayColumnsFromRelationship)
        {
            $q = DB::table(MonthlyPurchasesReportsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    MonthlyPurchasesReportsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->where(self::STATUS_COLUMN, BaseRepository::OPERATORS['EQUALS'], $status)
                ->orderByDesc(MonthlyPurchasesReportsRepository::ID_JOINED);


            $result = $q->get();
            return collect($result)->map(fn($item) => MonthlyPurchasesReportData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }



    public function updateStatus($id, string $status): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,];

        return $this->update($conditions, [
            'status'     =>   $status,
        ]);
    }



    public function updateLinkReport($id, string $link): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,];

        return $this->update($conditions, [
            'link_report'  =>   $link,
        ]);
    }



    public function updatePurchaseAmount($id, float $amount): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,];

        return $this->update($conditions, [
            'amount'  =>   $amount,
        ]);
    }
}
