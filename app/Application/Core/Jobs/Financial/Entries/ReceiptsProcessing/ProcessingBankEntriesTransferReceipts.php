<?php

namespace App\Application\Core\Jobs\Financial\Entries\ReceiptsProcessing;

use App\Domain\CentralDomain\Plans\Actions\GetPlansAction;
use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Entries\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\GetEntryByTimestampValueCpfAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateIdentificationPendingEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateReceiptLinkEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateTimestampValueCPFEntryAction;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Services\Atos8\Financial\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
use DateTime;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesByPlanIdAction;
use Domain\CentralDomain\Plans\Actions\GetPlanByNameAction;
use Domain\Ecclesiastical\Groups\Actions\GetFinancialGroupAction;
use Domain\Ecclesiastical\Groups\Actions\GetReturnReceivingGroupAction;
use Domain\Financial\Entries\Consolidation\Actions\CheckConsolidationStatusAction;
use Domain\Financial\ReceiptProcessing\Actions\CreateReceiptProcessing;
use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Domain\Financial\Reviewers\Actions\GetReviewerAction;
use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Domain\Secretary\Membership\Actions\GetMemberByCPFAction;
use Domain\Secretary\Membership\Actions\GetMemberByMiddleCPFAction;
use Domain\Secretary\Membership\Actions\UpdateMiddleCpfMemberAction;
use Domain\SyncStorage\Actions\GetSyncStorageDataAction;
use Domain\SyncStorage\Actions\UpdateStatusAction;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PlanRepository;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Infrastructure\Util\Storage\S3\UploadFile;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class ProcessingBankEntriesTransferReceipts
{
    private CreateEntryAction $createEntryAction;
    private GetMemberByMiddleCPFAction $getMemberByMiddleCPFAction;
    private GetEntryByTimestampValueCpfAction $getEntryByTimestampValueCpfAction;
    private GetSyncStorageDataAction $getSyncStorageDataAction;
    private UpdateMiddleCpfMemberAction $updateMiddleCpfMemberAction;
    private OCRExtractDataBankReceiptService $OCRExtractDataBankReceiptService;
    private UpdateIdentificationPendingEntryAction $updateIdentificationPendingEntryAction;
    private UpdateReceiptLinkEntryAction $updateReceiptLinkEntryAction;
    private UpdateTimestampValueCPFEntryAction $updateTimestampValueCPFEntryAction;
    private GetReturnReceivingGroupAction $getReturnReceivingGroupAction;
    private GetMemberByCPFAction $getMemberByCPFAction;
    private CreateReceiptProcessing $createReceiptProcessing;
    private UploadFile $uploadFile;
    private ConsolidationEntriesData $consolidationEntriesData;
    private EntryData $entryData;
    private  MemberData $memberData;
    private GetPlansAction $getPlansAction;
    private GetPlanByNameAction $getPlanByNameAction;
    private ReceiptProcessingData $receiptProcessingData;

    private GetChurchesByPlanIdAction $getChurchesByPlanIdAction;
    private GetReviewerAction $getReviewerAction;
    private GetFinancialGroupAction $getFinancialGroupAction;

    private UpdateStatusAction $updateStatusAction;
    private CheckConsolidationStatusAction $checkConsolidationStatusAction;

    private string $entryType;
    protected Collection $foldersData;
    protected Collection $syncStorageData;

    private bool $devolution = false;
    private int $amount = 0;
    private string $institution;
    private string $reason;

    private ?int $groupReturnedId = null;
    private ?int $groupReceivedId = null;

    private MinioStorageService $minioStorageService;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';
    const IDENTIFICATION_PENDING_1 = 1;
    const IDENTIFICATION_PENDING_0 = 0;
    const S3_ENTRIES_RECEIPT_PATH = 'entries/assets/receipts';
    const SYNC_STORAGE_ENTRIES_ERROR_RECEIPTS = 'sync_storage/financial/error_receipts/entries';
    const SUFFIX_TIMEZONE = 'T03:00:00.000Z';
    const SHARED_RECEIPTS_FOLDER_NAME = 'shared_receipts';
    const STORED_RECEIPTS_FOLDER_NAME = 'stored_receipts';
    const SYNC_STORAGE_FOLDER_NAME = 'sync_storage';
    const FINANCIAL_FOLDER_NAME = 'financial';
    const ENTRIES_FOLDER_NAME = 'entries';
    const TITHE_FOLDER_NAME = 'tithe';
    const DESIGNATED_FOLDER_NAME = 'designated';
    const OFFER_FOLDER_NAME = 'offer';


    public function __construct(
        CreateEntryAction                      $createEntryAction,
        GetMemberByMiddleCPFAction             $getMemberByMiddleCPFAction,
        GetMemberByCPFAction                   $getMemberByCPFAction,
        UpdateMiddleCpfMemberAction            $updateMiddleCpfMemberAction,
        GetEntryByTimestampValueCpfAction      $getEntryByTimestampValueCpfAction,
        GetSyncStorageDataAction               $getSyncStorageDataAction,
        GetReturnReceivingGroupAction          $getReturnReceivingGroupAction,
        UploadFile                             $uploadFile,
        UpdateIdentificationPendingEntryAction $updateIdentificationPendingEntryAction,
        UpdateReceiptLinkEntryAction           $updateReceiptLinkEntryAction,
        UpdateTimestampValueCPFEntryAction     $updateTimestampValueCPFEntryAction,
        EntryData                              $entryData,
        MemberData                             $memberData,
        OCRExtractDataBankReceiptService       $OCRExtractDataBankReceiptService,
        ConsolidationEntriesData               $consolidationEntriesData,
        GetPlansAction                         $getPlansAction,
        GetPlanByNameAction                    $getPlanByNameAction,
        GetChurchesByPlanIdAction              $getChurchesByPlanIdAction,
        GetReviewerAction                      $getReviewerAction,
        GetFinancialGroupAction                $getFinancialGroupAction,
        MinioStorageService                    $minioStorageService,
        UpdateStatusAction                     $updateStatusAction,
        CheckConsolidationStatusAction         $checkConsolidationStatusAction,
        ReceiptProcessingData                  $receiptProcessingData,
        CreateReceiptProcessing                $createReceiptProcessing
    )
    {
        $this->createEntryAction = $createEntryAction;
        $this->getMemberByMiddleCPFAction = $getMemberByMiddleCPFAction;
        $this->getSyncStorageDataAction = $getSyncStorageDataAction;
        $this->updateMiddleCpfMemberAction = $updateMiddleCpfMemberAction;
        $this->updateIdentificationPendingEntryAction = $updateIdentificationPendingEntryAction;
        $this->getReturnReceivingGroupAction = $getReturnReceivingGroupAction;
        $this->updateReceiptLinkEntryAction = $updateReceiptLinkEntryAction;
        $this->updateTimestampValueCPFEntryAction = $updateTimestampValueCPFEntryAction;
        $this->getEntryByTimestampValueCpfAction = $getEntryByTimestampValueCpfAction;
        $this->getMemberByCPFAction = $getMemberByCPFAction;
        $this->uploadFile = $uploadFile;
        $this->entryData = $entryData;
        $this->memberData = $memberData;
        $this->consolidationEntriesData = $consolidationEntriesData;
        $this->OCRExtractDataBankReceiptService = $OCRExtractDataBankReceiptService;
        $this->getPlansAction = $getPlansAction;
        $this->getPlanByNameAction = $getPlanByNameAction;
        $this->getChurchesByPlanIdAction = $getChurchesByPlanIdAction;
        $this->getReviewerAction = $getReviewerAction;
        $this->getFinancialGroupAction = $getFinancialGroupAction;
        $this->minioStorageService = $minioStorageService;
        $this->updateStatusAction = $updateStatusAction;
        $this->checkConsolidationStatusAction = $checkConsolidationStatusAction;
        $this->receiptProcessingData = $receiptProcessingData;
        $this->createReceiptProcessing = $createReceiptProcessing;
    }


    /**
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws Exception
     * @throws TenantCouldNotBeIdentifiedById
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        try
        {
            $tenants = $this->getTenantsByPlan(PlanRepository::PLAN_GOLD_NAME);

            foreach ($tenants as $tenant) {
                tenancy()->initialize($tenant);

                $this->syncStorageData = $this->getSyncStorageDataAction->execute(SyncStorageRepository::ENTRIES_VALUE_DOC_TYPE);

                foreach ($this->syncStorageData as $data) {
                    $this->process($data, $tenant);
                }
            }
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }

    }



    /**
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws Exception
     * @throws BindingResolutionException
     */
    private function process(SyncStorageData $data, $tenant): void
    {
        $basePathTemp = self::STORAGE_BASE_PATH . "tenants/{$tenant}/temp";
        $this->minioStorageService->deleteFilesInLocalDirectory($basePathTemp);

        $downloadedFile = $this->minioStorageService->downloadFile($data->path, $tenant, $basePathTemp);

        if (is_array($downloadedFile)) {
            $this->processFile($downloadedFile, $data, $tenant);
        }
    }



    /**
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws BindingResolutionException
     */
    private function processFile(array $downloadedFile, SyncStorageData $syncStorageData, string $tenant): void
    {
        $extractedData = $this->OCRExtractDataBankReceiptService->ocrExtractData($downloadedFile, $syncStorageData->docType, $syncStorageData->docSubType);

        if (count($extractedData) > 0 && $extractedData['status'] == 'SUCCESS')
        {
            $timestampValueCpf = $extractedData['data']['timestamp_value_cpf'];
            $middleCpf = $extractedData['data']['middle_cpf'];
            $member = $this->findMember($middleCpf);

            if ($this->isDuplicateEntry($timestampValueCpf)){
                $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::DUPLICATED_RECEIPT_VALUE);
                $this->minioStorageService->delete($syncStorageData->path, $tenant);
                return;
            }

            $this->setEntryData($extractedData, $member, $syncStorageData);

            $entry = $this->createEntryAction->execute($this->entryData, $this->consolidationEntriesData);
            $this->updateTimestampValueCPFEntryAction->execute($entry->id, $timestampValueCpf);

            if($extractedData['data']['doc_sub_type'] == EntryRepository::TITHE_VALUE && !$member)
                $this->updateIdentificationPendingEntryAction->execute($entry->id, self::IDENTIFICATION_PENDING_1);

            $sharedPath = $syncStorageData->path;
            $syncStorageData->path = str_replace(self::SHARED_RECEIPTS_FOLDER_NAME, self::STORED_RECEIPTS_FOLDER_NAME, $syncStorageData->path);
            $urlParts = explode('/', $syncStorageData->path);
            array_pop($urlParts);
            $path = implode('/', $urlParts);
            $fileUrl = $this->minioStorageService->upload($downloadedFile['fileUploaded'], $path, $tenant);

            if(!empty($fileUrl))
                $this->minioStorageService->delete($sharedPath, $tenant);

            $this->updateReceiptLinkEntryAction->execute($entry->id, $fileUrl);
            $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::DONE_VALUE);

        }
        else if(count($extractedData) > 0 && $extractedData['status'] != 'SUCCESS')
        {
            $linkReceipt = $this->minioStorageService->upload($downloadedFile['fileUploaded'], self::SYNC_STORAGE_ENTRIES_ERROR_RECEIPTS, $tenant, true);

            if(!empty($linkReceipt))
                $this->minioStorageService->delete($syncStorageData->path, $tenant);

            $this->setReceiptProcessingData($extractedData, $syncStorageData, $linkReceipt);
            $this->createReceiptProcessing->execute($this->receiptProcessingData);
            $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::ERROR_VALUE);
        }
    }


    /**
     * @param $extractedData
     * @param mixed $data
     * @param string $linkReceipt
     * @return void
     * @throws Throwable
     */
    public function setReceiptProcessingData($extractedData, SyncStorageData $data, string $linkReceipt): void
    {
        $reviewer = $this->getReviewerAction->execute();
        $financialGroup = $this->getFinancialGroupAction->execute();

        $this->receiptProcessingData = ReceiptProcessingData::fromExtractedData(
            $data, $extractedData, $linkReceipt, $reviewer, $financialGroup
        );
    }



    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException
     */
    private function findMember(string $middleCpf): ?Model
    {
        if (empty($middleCpf)) {
            return null;
        }

        $member = $this->getMemberByMiddleCPFAction->execute($middleCpf);

        if (!$member)
        {
            $member = $this->getMemberByCPFAction->execute($middleCpf);

            if ($member)
                $this->updateMiddleCpfMemberAction->execute($member->id, $middleCpf);
        }

        return $member;
    }


    /**
     * @param string $timestampValueCpf
     * @return bool
     * @throws Throwable
     */
    private function isDuplicateEntry(string $timestampValueCpf): bool
    {
        if (empty($timestampValueCpf)) {
            return false;
        }

        $entry = $this->getEntryByTimestampValueCpfAction->execute($timestampValueCpf);
        if ($entry) {
            return true;
        }

        return false;
    }


    /**
     * @param string $date
     * @return bool
     * @throws Throwable
     */
    private function isEntryInsertionInClosedMonth(string $date): bool
    {
        return $this->checkConsolidationStatusAction->execute($date);
    }



    /**
     * @return int|null
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function getReturnReceivingGroup(): int | null
    {
        $group = $this->getReturnReceivingGroupAction->execute();

        if(!is_null($group))
        {
            return $group->id;
        }
        else
        {
            return null;
        }
    }



    /**
     *
     * @throws \Exception
     */
    function getNextBusinessDay($date): string
    {
        $holidays = [
            '01-01', // Confraternização Universal (Ano Novo)
            '04-21', // Tiradentes
            '05-01', // Dia do Trabalho
            '09-07', // Independência do Brasil
            '10-12', // Aparecida
            '11-02', // Finados
            '11-15', // Proclamação da República
            '12-25', // Natal
        ];

        $currentDate = DateTime::createFromFormat('d/m/Y', $date);
        $dayOfWeek = $currentDate->format('N');
        $monthDay = $currentDate->format('m-d');


        if (in_array($dayOfWeek, [6, 7]) || in_array($monthDay, $holidays)) {
            do
            {
                $currentDate->modify('+1 day');
                $dayOfWeek = $currentDate->format('N');
                $monthDay = $currentDate->format('m-d');
            }
            while (in_array($dayOfWeek, [6, 7]) || in_array($monthDay, $holidays));

            return $currentDate->format('Y-m-d');
        }

        return $currentDate->format('Y-m-d');
    }



    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function getTenantsByPlan(string $planName): array
    {
        $arrTenants = [];
        $plan = $this->getPlanByNameAction->execute($planName);

        if(!is_null($plan))
        {
            $tenants = $this->getChurchesByPlanIdAction->execute($plan->id);

            if(count($tenants) > 0)
            {
                foreach ($tenants as $tenant)
                    $arrTenants[] = $tenant->tenant_id;

                return $arrTenants;
            }
        }
        else
        {
            return $arrTenants;
        }
    }



    /**
     *
     * @throws \Exception
     * @throws Throwable
     */
    public function setEntryData(array $extractedData, mixed $member, SyncStorageData $data): void
    {
        $reviewer = $this->getReviewerAction->execute();
        $returnReceivingGroupId = $this->getReturnReceivingGroup();
        $nextBusinessDay = $this->getNextBusinessDay($extractedData['data']['date']);

        $this->entryData = EntryData::fromExtractedData(
            $extractedData,
            $member,
            $data,
            $reviewer,
            $returnReceivingGroupId,
            $nextBusinessDay
        );

        $this->consolidationEntriesData->date = DateTime::createFromFormat('d/m/Y', $extractedData['data']['date'])->format('Y-m-d');
    }
}
