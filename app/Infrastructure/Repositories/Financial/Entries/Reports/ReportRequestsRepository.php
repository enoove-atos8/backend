<?php

namespace Infrastructure\Repositories\Financial\Entries\Reports;

use App\Domain\Financial\Entries\Reports\DataTransferObjects\ReportRequestsData;
use App\Domain\Financial\Entries\Reports\Interfaces\ReportRequestsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class ReportRequestsRepository extends BaseRepository implements ReportRequestsRepositoryInterface
{

    protected mixed $model = ReportRequests::class;

    const TABLE_NAME = 'entries_report_requests';
    const STATUS_COLUMN = 'status';
    const DATES_COLUMN = 'dates';
    const TO_PROCESS_STATUS_VALUE = 'to_process';
    const IN_PROGRESS_STATUS_VALUE = 'in_progress';
    const DONE_STATUS_VALUE = 'done';
    const ERROR_STATUS_VALUE = 'error';
    const MONTHLY_ENTRIES_REPORT_NAME = 'monthly_entries';
    const MONTHLY_RECEIPTS_REPORT_NAME = 'monthly_receipts';
    const QUARTERLY_ENTRIES_REPORT_NAME = 'quarterly_entries';


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
            'all_groups_receipts'   => $reportJobData->allGroupsReceipts
        ]);
    }



    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getReports(): Collection
    {
        $this->requiredRelationships = ['user'];

        $this->queryConditions = [];

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
}
