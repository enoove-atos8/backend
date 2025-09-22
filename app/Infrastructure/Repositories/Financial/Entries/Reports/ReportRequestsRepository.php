<?php

namespace Infrastructure\Repositories\Financial\Entries\Reports;

use App\Domain\Financial\Entries\Reports\DataTransferObjects\ReportRequestsData;
use App\Domain\Financial\Entries\Reports\Interfaces\ReportRequestsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use App\Infrastructure\Repositories\Accounts\User\UserDetailRepository;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\Movements\MovementRepository;

class ReportRequestsRepository extends BaseRepository implements ReportRequestsRepositoryInterface
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
    const START_BY_COLUMN = 'entries_report_requests.started_by';
    const QUARTERLY_ENTRIES_REPORT_NAME = 'quarterly_entries';

    const PAGINATE_NUMBER = 30;


    const DISPLAY_SELECT_COLUMNS = [
        'entries_report_requests.id as reports_id',
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
        'entries_report_requests.link_report as reports_link_report',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];



    /**
     * @param ReportRequestsData $reportJobData
     * @return ReportRequests
     */
    public function generateReport(ReportRequestsData $reportJobData): ReportRequests
    {
        return $this->create([
            'group_received_id'     => $reportJobData->groupReceivedId,
            'started_by'            => $reportJobData->startedBy,
            'report_name'           => $reportJobData->reportName,
            'detailed_report'       => $reportJobData->detailedReport,
            'generation_date'       => $reportJobData->generationDate,
            'dates'                 => $reportJobData->dates,
            'status'                => $reportJobData->status,
            'error'                 => $reportJobData->error,
            'entry_types'           => $reportJobData->entryTypes,
            'date_order'            => $reportJobData->dateOrder,
            'all_groups_receipts'   => $reportJobData->allGroupsReceipts,
            'include_cash_deposit'  => $reportJobData->includeCashDeposit
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
        );

        $query = function () use (
            $paginate, $displayColumnsFromRelationship) {

            $q = DB::table(ReportRequestsRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    UserDetailRepository::TABLE_NAME,
                    ReportRequestsRepository::START_BY_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    UserDetailRepository::USER_ID_COLUMN)
                ->leftJoin(
                    GroupsRepository::TABLE_NAME,
                    ReportRequestsRepository::GROUP_RECEIVED_ID_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    GroupsRepository::ID_COLUMN_JOINED)
                ->orderByDesc(ReportRequestsRepository::ID_JOINED);


            if (!$paginate)
            {
                $result = $q->get();
                return collect($result)->map(fn($item) => ReportRequestsData::fromResponse((array) $item));
            }
            else
            {
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $result->setCollection(
                    $result->getCollection()->map(fn($item) => ReportRequestsData::fromResponse((array) $item))
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
        $this->requiredRelationships = ['user'];

        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::STATUS_COLUMN, $status, 'and');

        return $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN,
            BaseRepository::ORDERS['DESC']
        );
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
}
