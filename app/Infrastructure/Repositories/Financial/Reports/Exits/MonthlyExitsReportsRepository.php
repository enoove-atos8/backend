<?php

namespace App\Infrastructure\Repositories\Financial\Reports\Exits;

use App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData;
use App\Domain\Financial\Reports\Exits\Interfaces\MonthlyExitsReportsRepositoryInterface;
use App\Domain\Financial\Reports\Exits\Models\ExitsReportRequests;
use App\Infrastructure\Repositories\Users\User\UserDetailRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountRepository;

class MonthlyExitsReportsRepository extends BaseRepository implements MonthlyExitsReportsRepositoryInterface
{

    protected mixed $model = ExitsReportRequests::class;

    const TABLE_NAME = 'exits_reports_request';
    const STATUS_COLUMN = 'status';
    const ID_JOINED = 'exits_reports_request.id';
    const DATES_COLUMN = 'dates';
    const TO_PROCESS_STATUS_VALUE = 'to_process';
    const IN_PROGRESS_STATUS_VALUE = 'in_progress';
    const DONE_STATUS_VALUE = 'done';
    const ERROR_STATUS_VALUE = 'error';
    const NO_DATA_STATUS_VALUE = 'no_data';
    const MONTHLY_EXITS_REPORT_NAME = 'monthly_exits';
    const MONTHLY_RECEIPTS_REPORT_NAME = 'monthly_receipts';
    const ACCOUNT_ID_JOINED = 'exits_reports_request.account_id';
    const START_BY_COLUMN = 'exits_reports_request.started_by';

    const PAGINATE_NUMBER = 30;


    const DISPLAY_SELECT_COLUMNS = [
        'exits_reports_request.id as reports_id',
        'exits_reports_request.account_id as reports_account_id',
        'exits_reports_request.started_by as reports_started_by',
        'exits_reports_request.report_name as reports_report_name',
        'exits_reports_request.detailed_report as reports_detailed_report',
        'exits_reports_request.generation_date as reports_generation_date',
        'exits_reports_request.dates as reports_dates',
        'exits_reports_request.status as reports_status',
        'exits_reports_request.error as reports_error',
        'exits_reports_request.exit_types as reports_exit_types',
        'exits_reports_request.date_order as reports_date_order',
        'exits_reports_request.link_report as reports_link_report',
        'exits_reports_request.amount as reports_amount',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param \App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData $monthlyExitsReportData
     * @return ExitsReportRequests
     */
    public function generateMonthlyExitsReport(MonthlyExitsReportData $monthlyExitsReportData): ExitsReportRequests
    {
        return $this->create([
            'account_id'                            => $monthlyExitsReportData->accountId,
            'started_by'                            => $monthlyExitsReportData->startedBy,
            'report_name'                           => $monthlyExitsReportData->reportName,
            'detailed_report'                       => $monthlyExitsReportData->detailedReport,
            'generation_date'                       => $monthlyExitsReportData->generationDate,
            'dates'                                 => $monthlyExitsReportData->dates,
            'status'                                => $monthlyExitsReportData->status,
            'error'                                 => $monthlyExitsReportData->error,
            'exit_types'                            => $monthlyExitsReportData->exitTypes,
            'date_order'                            => $monthlyExitsReportData->dateOrder,
        ]);
    }


    /**
     * @param \App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData $monthlyExitsReportData
     * @return ExitsReportRequests
     */
    public function generateMonthlyExitsReceiptsReport(MonthlyExitsReportData $monthlyExitsReportData): ExitsReportRequests
    {
        return $this->create([
            'account_id'                            => $monthlyExitsReportData->accountId,
            'started_by'                            => $monthlyExitsReportData->startedBy,
            'report_name'                           => $monthlyExitsReportData->reportName,
            'detailed_report'                       => $monthlyExitsReportData->detailedReport,
            'generation_date'                       => $monthlyExitsReportData->generationDate,
            'dates'                                 => $monthlyExitsReportData->dates,
            'status'                                => $monthlyExitsReportData->status,
            'error'                                 => $monthlyExitsReportData->error,
            'exit_types'                            => $monthlyExitsReportData->exitTypes,
            'date_order'                            => $monthlyExitsReportData->dateOrder,
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
            AccountRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use (
            $paginate, $displayColumnsFromRelationship) {

            $q = DB::table(MonthlyExitsReportsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    MonthlyExitsReportsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->leftJoin(
                    AccountRepository::TABLE_NAME,
                    MonthlyExitsReportsRepository::ACCOUNT_ID_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    AccountRepository::ID_COLUMN_JOINED)
                ->orderByDesc(MonthlyExitsReportsRepository::ID_JOINED);


            if (!$paginate)
            {
                $result = $q->get();
                return collect($result)->map(fn($item) => MonthlyExitsReportData::fromResponse((array) $item));
            }
            else
            {
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $result->setCollection(
                    $result->getCollection()->map(fn($item) => MonthlyExitsReportData::fromResponse((array) $item))
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
            $q = DB::table(MonthlyExitsReportsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    MonthlyExitsReportsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->where(self::STATUS_COLUMN, BaseRepository::OPERATORS['EQUALS'], $status)
                ->orderByDesc(MonthlyExitsReportsRepository::ID_JOINED);


            $result = $q->get();
            return collect($result)->map(fn($item) => MonthlyExitsReportData::fromResponse((array) $item));
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



    public function updateExitAmount($id, float $amount): mixed
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
