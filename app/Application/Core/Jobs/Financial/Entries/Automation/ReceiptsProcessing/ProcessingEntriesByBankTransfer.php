<?php

namespace Application\Core\Jobs\Financial\Entries\Automation\ReceiptsProcessing;

use App\Domain\CentralDomain\Plans\Actions\GetPlansAction;
use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Entries\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\GetEntryByTimestampValueCpfAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateIdentificationPendingEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateReceiptLinkEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateTimestampValueCPFEntryAction;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Members\Actions\GetMemberByCPFAction;
use App\Domain\Members\Actions\GetMemberByMiddleCPFAction;
use App\Domain\Members\Actions\UpdateMiddleCpfMemberAction;
use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Services\Atos8\Financial\Entries\Automation\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
use DateTime;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesByPlanIdAction;
use Domain\CentralDomain\Plans\Actions\GetPlanByNameAction;
use Domain\Ecclesiastical\Groups\Actions\GetFinancialGroupAction;
use Domain\Ecclesiastical\Groups\Actions\GetReturnReceivingGroupAction;
use Domain\Financial\Receipts\Entries\ReadingError\Actions\CreateReadingErrorReceiptAction;
use Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects\ReadingErrorReceiptData;
use Domain\Financial\Reviewers\Actions\GetReviewerAction;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\SyncStorage\Actions\GetSyncStorageDataAction;
use Domain\SyncStorage\Actions\UpdateStatusAction;
use Google\Service\Exception;
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

class ProcessingEntriesByBankTransfer
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
    private CreateReadingErrorReceiptAction $createReadingErrorReceiptAction;
    private GetReturnReceivingGroupAction $getReturnReceivingGroupAction;
    private GetMemberByCPFAction $getMemberByCPFAction;
    private UploadFile $uploadFile;
    private ConsolidationEntriesData $consolidationEntriesData;
    private EntryData $entryData;
    private ReadingErrorReceiptData $readingErrorReceiptData;
    private  MemberData $memberData;
    private GetPlansAction $getPlansAction;
    private GetPlanByNameAction $getPlanByNameAction;

    private GetChurchesByPlanIdAction $getChurchesByPlanIdAction;
    private GetReviewerAction $getReviewerAction;
    private GetFinancialGroupAction $getFinancialGroupAction;

    private UpdateStatusAction $updateStatusAction;

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
    const SUFIX_TIMEZONE = 'T03:00:00.000Z';
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
        ReadingErrorReceiptData                $readingErrorReceiptData,
        MemberData                             $memberData,
        OCRExtractDataBankReceiptService       $OCRExtractDataBankReceiptService,
        ConsolidationEntriesData               $consolidationEntriesData,
        CreateReadingErrorReceiptAction        $createReadingErrorReceiptAction,
        GetPlansAction                         $getPlansAction,
        GetPlanByNameAction                    $getPlanByNameAction,
        GetChurchesByPlanIdAction              $getChurchesByPlanIdAction,
        GetReviewerAction                      $getReviewerAction,
        GetFinancialGroupAction                $getFinancialGroupAction,
        MinioStorageService                    $minioStorageService,
        UpdateStatusAction                     $updateStatusAction
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
        $this->readingErrorReceiptData = $readingErrorReceiptData;
        $this->uploadFile = $uploadFile;
        $this->entryData = $entryData;
        $this->memberData = $memberData;
        $this->consolidationEntriesData = $consolidationEntriesData;
        $this->OCRExtractDataBankReceiptService = $OCRExtractDataBankReceiptService;
        $this->createReadingErrorReceiptAction = $createReadingErrorReceiptAction;
        $this->getPlansAction = $getPlansAction;
        $this->getPlanByNameAction = $getPlanByNameAction;
        $this->getChurchesByPlanIdAction = $getChurchesByPlanIdAction;
        $this->getReviewerAction = $getReviewerAction;
        $this->getFinancialGroupAction = $getFinancialGroupAction;
        $this->minioStorageService = $minioStorageService;
        $this->updateStatusAction = $updateStatusAction;
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
        $this->minioStorageService->delete($data->path, $tenant);

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
        $extractedData = $this->OCRExtractDataBankReceiptService->ocrExtractData($downloadedFile, $syncStorageData->docSubType);

        if (count($extractedData) > 0 && $extractedData['status'] == 'SUCCESS')
        {
            $timestampValueCpf = $extractedData['data']['timestamp_value_cpf'];
            $middleCpf = $extractedData['data']['middle_cpf'];
            $member = $this->findMember($middleCpf);

            if ($this->isDuplicateEntry($timestampValueCpf))
                return;

            $this->setEntryData($extractedData, $member, $syncStorageData);

            $entry = $this->createEntryAction->execute($this->entryData, $this->consolidationEntriesData);
            $this->updateTimestampValueCPFEntryAction->execute($entry->id, $timestampValueCpf);

            if($extractedData['data']['entry_type'] == EntryRepository::TITHE_VALUE && !$member)
                $this->updateIdentificationPendingEntryAction->execute($entry->id, self::IDENTIFICATION_PENDING_1);


            $syncStorageData->path = str_replace(self::SHARED_RECEIPTS_FOLDER_NAME, self::STORED_RECEIPTS_FOLDER_NAME, $syncStorageData->path);
            $urlParts = explode('/', $syncStorageData->path);
            array_pop($urlParts);
            $path = implode('/', $urlParts);
            $fileUrl = $this->minioStorageService->upload($downloadedFile['fileUploaded'], $path, $tenant);

            $this->updateReceiptLinkEntryAction->execute($entry->id, $fileUrl);

            $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::DONE_VALUE);

        }
        else if(count($extractedData) > 0 && $extractedData['status'] != 'SUCCESS')
        {
            $fileUploaded = $this->minioStorageService->upload($downloadedFile['fileUploaded'], self::SYNC_STORAGE_ENTRIES_ERROR_RECEIPTS, $tenant, true);

            $this->configReadingErrorReceiptData($extractedData, $syncStorageData);

            $this->setReadingErrorReceiptData(
                $this->groupReturnedId,
                $this->groupReceivedId,
                $this->entryType,
                $this->amount,
                $this->institution,
                $this->reason,
                $this->devolution,
                $fileUploaded);

            $this->createReadingErrorReceiptAction->execute($this->readingErrorReceiptData);

            $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::ERROR_VALUE);
        }
    }



    /**
     * @param string|null $groupReturnedId
     * @param string|null $groupReceivedId
     * @param string $entryType
     * @param string|null $amount
     * @param string|null $institution
     * @param string|null $reason
     * @param bool $devolution
     * @param string|null $receiptLink
     * @return void
     */
    public function setReadingErrorReceiptData(
        string | null $groupReturnedId,
        string | null $groupReceivedId,
        string $entryType,
        string | null $amount,
        string | null $institution,
        string | null $reason,
        bool $devolution,
        string | null $receiptLink): void
    {
        $this->readingErrorReceiptData->groupReturnedId = $groupReturnedId;
        $this->readingErrorReceiptData->groupReceivedId = $groupReceivedId;
        $this->readingErrorReceiptData->entryType = $entryType;
        $this->readingErrorReceiptData->amount = $amount != null ? floatval($amount) / 100 : null;
        $this->readingErrorReceiptData->institution = $institution;
        $this->readingErrorReceiptData->reason = $reason;
        $this->readingErrorReceiptData->devolution = $devolution;
        $this->readingErrorReceiptData->deleted = 0;
        $this->readingErrorReceiptData->receiptLink = $receiptLink;
    }


    /**
     * @param $extractedData
     * @param mixed $data
     * @return void
     * @throws Throwable
     */
    public function configReadingErrorReceiptData($extractedData, SyncStorageData $data): void
    {
        $this->entryType = $data->docSubType;
        $this->devolution = $data->isDevolution == 1;
        $this->reason = $extractedData['status'];
        $this->amount = $extractedData['data']['amount'] != 0 ? $extractedData['data']['amount'] : 0;
        $this->institution = $extractedData['data']['institution'] != '' ? $extractedData['data']['institution'] : null;

        if($this->devolution)
        {
            $financialGroup = $this->getFinancialGroupAction->execute();
            $this->groupReceivedId = $financialGroup->id;
            $this->groupReturnedId = $data->groupId != 0 ? $data->groupId : null;
        }
        else
        {
            $this->groupReceivedId = $data->groupId != 0 ? $data->groupId : null;
            $this->groupReturnedId = null;
        }
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
            '05-30', // Corpus Christi
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

        $currentDate = date('Y-m-d');
        $extractedDate = $extractedData['data']['date'];

        $this->entryData->amount = floatval($extractedData['data']['amount']) / 100;
        $this->entryData->comments = 'Entrada registrada automaticamente!';
        $this->entryData->dateEntryRegister = $currentDate;
        $this->entryData->dateTransactionCompensation = $this->getNextBusinessDay($extractedDate) . self::SUFIX_TIMEZONE;
        $this->entryData->deleted = 0;
        $this->entryData->entryType = $data->docSubType;
        $this->entryData->memberId = $member?->id;
        $this->entryData->receipt = null;
        $this->entryData->devolution = 0;
        $this->entryData->residualValue = 0;
        $this->entryData->identificationPending = 0;
        $this->entryData->cultId = null;
        $this->entryData->timestampValueCpf = null;

        if($data->docSubType == EntryRepository::DESIGNATED_VALUE)
        {
            $this->entryData->groupReceivedId = $data->groupId;

            if($data->isDevolution == 1)
            {
                $this->entryData->devolution = 1;
                $this->entryData->groupReceivedId = $this->getReturnReceivingGroup();
                $this->entryData->groupReturnedId = $data->groupId;
            }
        }

        $this->entryData->reviewerId = $reviewer->id;
        $this->entryData->transactionCompensation = EntryRepository::COMPENSATED_VALUE;
        $this->entryData->transactionType = EntryRepository::PIX_TRANSACTION_TYPE;

        $this->consolidationEntriesData->date = DateTime::createFromFormat('d/m/Y', $extractedData['data']['date'])->format('Y-m-d');

    }
}
