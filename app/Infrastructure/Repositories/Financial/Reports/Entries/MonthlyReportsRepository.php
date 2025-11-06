<?php

namespace App\Infrastructure\Repositories\Financial\Reports\Entries;

use App\Domain\Financial\Reports\Entries\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;
use App\Domain\Financial\Reports\Entries\Models\ReportRequests;
use App\Infrastructure\Repositories\Accounts\User\UserDetailRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountRepository;

class MonthlyReportsRepository extends BaseRepository implements MonthlyReportsRepositoryInterface
{

    protected mixed $model = ReportRequests::class;

    const TABLE_NAME = 'entries_report_requests';
    const STATUS_COLUMN = 'status';
    const ID_JOINED = 'entries_report_requests.id';
    const DATES_COLUMN = 'dates';
    const TO_PROCESS_STATUS_VALUE = 'to_process';
    const IN_PROGRESS_STATUS_VALUE = 'in_progress';
    const DONE_STATUS_VALUE = 'done';
    const ERROR_STATUS_VALUE = 'error';
    const NO_RECEIPTS_STATUS_VALUE = 'no_data';
    const MONTHLY_ENTRIES_REPORT_NAME = 'monthly_entries';
    const MONTHLY_RECEIPTS_REPORT_NAME = 'monthly_receipts';
    const GROUP_RECEIVED_ID = 'group_received_id';
    const GROUP_RECEIVED_ID_JOINED = 'entries_report_requests.group_received_id';
    const ACCOUNT_ID_JOINED = 'entries_report_requests.account_id';
    const START_BY_COLUMN = 'entries_report_requests.started_by';
    const QUARTERLY_ENTRIES_REPORT_NAME = 'quarterly_entries';

    const PAGINATE_NUMBER = 30;


    const DISPLAY_SELECT_COLUMNS = [
        'entries_report_requests.id as reports_id',
        'entries_report_requests.account_id as reports_account_id',
        'entries_report_requests.group_received_id as reports_group_received_id',
        'entries_report_requests.started_by as reports_started_by',
        'entries_report_requests.report_name as reports_report_name',
        'entries_report_requests.detailed_report as reports_detailed_report',
        'entries_report_requests.generation_date as reports_generation_date',
        'entries_report_requests.dates as reports_dates',
        'entries_report_requests.status as reports_status',
        'entries_report_requests.error as reports_error',
        'entries_report_requests.entry_types as reports_entry_types',
        'entries_report_requests.date_order as reports_date_order',
        'entries_report_requests.all_groups_receipts as reports_all_groups_receipts',
        'entries_report_requests.include_cash_deposit as reports_include_cash_deposit',
        'entries_report_requests.tithe_amount as reports_tithe_amount',
        'entries_report_requests.designated_amount as reports_designated_amount',
        'entries_report_requests.offers_amount as reports_offer_amount',
        'entries_report_requests.monthly_entries_amount as reports_monthly_entries_amount',
        'entries_report_requests.include_groups_entries as reports_include_groups_entries',
        'entries_report_requests.include_anonymous_offers as reports_include_anonymous_offers',
        'entries_report_requests.include_transfers_between_accounts as reports_include_transfers_between_accounts',
        'entries_report_requests.link_report as reports_link_report',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param MonthlyReportData $monthlyReportData
     * @return ReportRequests
     */
    public function generateMonthlyReceiptsReport(MonthlyReportData $monthlyReportData): ReportRequests
    {
        return $this->create([
            'account_id'                            => $monthlyReportData->accountId,
            'group_received_id'                     => $monthlyReportData->groupReceivedId,
            'started_by'                            => $monthlyReportData->startedBy,
            'report_name'                           => $monthlyReportData->reportName,
            'detailed_report'                       => $monthlyReportData->detailedReport,
            'generation_date'                       => $monthlyReportData->generationDate,
            'dates'                                 => $monthlyReportData->dates,
            'status'                                => $monthlyReportData->status,
            'error'                                 => $monthlyReportData->error,
            'entry_types'                           => $monthlyReportData->entryTypes,
            'date_order'                            => $monthlyReportData->dateOrder,
            'all_groups_receipts'                   => $monthlyReportData->allGroupsReceipts,
            'include_cash_deposit'                  => $monthlyReportData->includeCashDeposit,
        ]);
    }




    /**
     * @param MonthlyReportData $monthlyReportData
     * @return ReportRequests
     */
    public function generateMonthlyEntriesReport(MonthlyReportData $monthlyReportData): ReportRequests
    {
        return $this->create([
            'account_id'                            => $monthlyReportData->accountId,
            'started_by'                            => $monthlyReportData->startedBy,
            'report_name'                           => $monthlyReportData->reportName,
            'generation_date'                       => $monthlyReportData->generationDate,
            'dates'                                 => $monthlyReportData->dates,
            'status'                                => $monthlyReportData->status,
            'include_groups_entries'                => $monthlyReportData->includeGroupsEntries,
            'include_anonymous_offers'              => $monthlyReportData->includeAnonymousOffers,
            'include_transfers_between_accounts'    => $monthlyReportData->includeTransfersBetweenAccounts,
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
            GroupsRepository::DISPLAY_SELECT_COLUMNS,
            AccountRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use (
            $paginate, $displayColumnsFromRelationship) {

            $q = DB::table(MonthlyReportsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    MonthlyReportsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->leftJoin(
                    GroupsRepository::TABLE_NAME,
                    MonthlyReportsRepository::GROUP_RECEIVED_ID_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    GroupsRepository::ID_COLUMN_JOINED)
                ->leftJoin(
                    AccountRepository::TABLE_NAME,
                    MonthlyReportsRepository::ACCOUNT_ID_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    AccountRepository::ID_COLUMN_JOINED)
                ->orderByDesc(MonthlyReportsRepository::ID_JOINED);


            if (!$paginate)
            {
                $result = $q->get();
                return collect($result)->map(fn($item) => MonthlyReportData::fromResponse((array) $item));
            }
            else
            {
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $result->setCollection(
                    $result->getCollection()->map(fn($item) => MonthlyReportData::fromResponse((array) $item))
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
            GroupsRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use ($status, $displayColumnsFromRelationship)
        {
            $q = DB::table(MonthlyReportsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    MonthlyReportsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->leftJoin(
                    GroupsRepository::TABLE_NAME,
                    MonthlyReportsRepository::GROUP_RECEIVED_ID_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    GroupsRepository::ID_COLUMN_JOINED)
                ->where(self::STATUS_COLUMN, BaseRepository::OPERATORS['EQUALS'], $status)
                ->orderByDesc(MonthlyReportsRepository::ID_JOINED);


            $result = $q->get();
            return collect($result)->map(fn($item) => MonthlyReportData::fromResponse((array) $item));
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



    public function updateEntryTypesAmount($id, array $entryTypesAmount): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,];

        return $this->update($conditions, [
            'tithe_amount'          =>   $entryTypesAmount['titheAmount'],
            'designated_amount'     =>   $entryTypesAmount['designatedAmount'],
            'offers_amount'         =>   $entryTypesAmount['offerAmount'],
        ]);
    }



    public function updateMonthlyEntriesAmount($id, string $amount): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,];

        return $this->update($conditions, [
            'monthly_entries_amount'  =>   $amount,
        ]);
    }
}
