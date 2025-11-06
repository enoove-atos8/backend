<?php

namespace App\Infrastructure\Repositories\Financial\Reports\Balances;

use App\Domain\Financial\Reports\Balances\DataTransferObjects\MonthlyBalancesReportData;
use App\Domain\Financial\Reports\Balances\Interfaces\MonthlyBalancesReportsRepositoryInterface;
use App\Domain\Financial\Reports\Balances\Models\BalancesReportRequests;
use App\Infrastructure\Repositories\Accounts\User\UserDetailRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountRepository;

class MonthlyBalancesReportsRepository extends BaseRepository implements MonthlyBalancesReportsRepositoryInterface
{
    protected mixed $model = BalancesReportRequests::class;

    const TABLE_NAME = 'balances_reports_request';

    const STATUS_COLUMN = 'status';

    const ID_JOINED = 'balances_reports_request.id';

    const DATES_COLUMN = 'dates';

    const TO_PROCESS_STATUS_VALUE = 'to_process';

    const IN_PROGRESS_STATUS_VALUE = 'in_progress';

    const DONE_STATUS_VALUE = 'done';

    const ERROR_STATUS_VALUE = 'error';

    const NO_DATA_STATUS_VALUE = 'no_data';

    const GENERAL_BALANCES_REPORT_NAME = 'general_balances';

    const MONTHLY_BALANCE_EVOLUTION_REPORT_NAME = 'monthly_balance_evolution';

    const ACCOUNT_ID_JOINED = 'balances_reports_request.account_id';

    const START_BY_COLUMN = 'balances_reports_request.started_by';

    const PAGINATE_NUMBER = 30;

    const DISPLAY_SELECT_COLUMNS = [
        'balances_reports_request.id as reports_id',
        'balances_reports_request.account_id as reports_account_id',
        'balances_reports_request.started_by as reports_started_by',
        'balances_reports_request.report_name as reports_report_name',
        'balances_reports_request.generation_date as reports_generation_date',
        'balances_reports_request.dates as reports_dates',
        'balances_reports_request.status as reports_status',
        'balances_reports_request.error as reports_error',
        'balances_reports_request.link_report as reports_link_report',
    ];

    /**
     * Array of conditions
     */
    private array $queryConditions = [];

    public function generateMonthlyBalancesReport(MonthlyBalancesReportData $monthlyBalancesReportData): BalancesReportRequests
    {
        return $this->create([
            'account_id' => $monthlyBalancesReportData->accountId,
            'started_by' => $monthlyBalancesReportData->startedBy,
            'report_name' => $monthlyBalancesReportData->reportName,
            'generation_date' => $monthlyBalancesReportData->generationDate,
            'dates' => $monthlyBalancesReportData->dates,
            'status' => $monthlyBalancesReportData->status,
            'error' => $monthlyBalancesReportData->error,
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getReports(bool $paginate = true): Collection|Paginator
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            UserDetailRepository::DISPLAY_SELECT_COLUMNS,
            AccountRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use (
            $paginate, $displayColumnsFromRelationship) {

            $q = DB::table(MonthlyBalancesReportsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    MonthlyBalancesReportsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->leftJoin(
                    AccountRepository::TABLE_NAME,
                    MonthlyBalancesReportsRepository::ACCOUNT_ID_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    AccountRepository::ID_COLUMN_JOINED)
                ->orderByDesc(MonthlyBalancesReportsRepository::ID_JOINED);

            if (! $paginate) {
                $result = $q->get();

                return collect($result)->map(fn ($item) => MonthlyBalancesReportData::fromResponse((array) $item));
            } else {
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $result->setCollection(
                    $result->getCollection()->map(fn ($item) => MonthlyBalancesReportData::fromResponse((array) $item))
                );

                return $result;
            }
        };

        return $this->doQuery($query);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getReportsByStatus(string $status): Collection
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            UserDetailRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use ($status, $displayColumnsFromRelationship) {
            $q = DB::table(MonthlyBalancesReportsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    MonthlyBalancesReportsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->where(self::STATUS_COLUMN, BaseRepository::OPERATORS['EQUALS'], $status)
                ->orderByDesc(MonthlyBalancesReportsRepository::ID_JOINED);

            $result = $q->get();

            return collect($result)->map(fn ($item) => MonthlyBalancesReportData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    public function updateStatus($id, string $status): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id, ];

        return $this->update($conditions, [
            'status' => $status,
        ]);
    }

    public function updateLinkReport($id, string $link): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id, ];

        return $this->update($conditions, [
            'link_report' => $link,
        ]);
    }
}
