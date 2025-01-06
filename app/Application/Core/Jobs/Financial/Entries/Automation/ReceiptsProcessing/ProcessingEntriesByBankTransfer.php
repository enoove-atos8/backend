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
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Services\Atos8\Financial\Entries\Automation\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
use App\Infrastructure\Services\External\GoogleDrive\GoogleDriveService;
use DateTime;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesByPlanIdAction;
use Domain\CentralDomain\Plans\Actions\GetPlanByNameAction;
use Domain\Ecclesiastical\Folders\Actions\GetEcclesiasticalGroupsFoldersAction;
use Domain\Ecclesiastical\Groups\Actions\GetReturnReceivingGroupAction;
use Domain\Financial\Receipts\Entries\ReadingError\Actions\CreateReadingErrorReceiptAction;
use Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects\ReadingErrorReceiptData;
use Domain\Financial\Reviewers\Actions\GetReviewerAction;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Models\Member;
use Google\Service\Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\CentralDomain\PlanRepository;
use Infrastructure\Util\Storage\S3\UploadFile;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use thiagoalessio\TesseractOCR\TesseractOcrException;
use Throwable;

class ProcessingEntriesByBankTransfer
{
    private GoogleDriveService $googleDriveService;
    private CreateEntryAction $createEntryAction;
    private GetMemberByMiddleCPFAction $getMemberByMiddleCPFAction;
    private GetEntryByTimestampValueCpfAction $getEntryByTimestampValueCpfAction;
    private GetEcclesiasticalGroupsFoldersAction $getEcclesiasticalGroupsFoldersAction;
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

    private string $entryType;
    protected Collection $foldersData;

    private bool $devolution = false;
    private int $amount = 0;
    private string $institution;
    private string $reason;

    private ?int $groupReturnedId = null;
    private ?int $groupReceivedId = null;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';
    const IDENTIFICATION_PENDING_1 = 1;
    const IDENTIFICATION_PENDING_0 = 0;
    const S3_ENTRIES_RECEIPT_PATH = 'entries/assets/receipts';
    const S3_ENTRIES_RECEIPT_UNIDENTIFIED_PATH = 'entries/assets/receipts/unidentified';
    const SUFIX_TIMEZONE = 'T03:00:00.000Z';


    public function __construct(
        GoogleDriveService $googleDriveService,
        CreateEntryAction $createEntryAction,
        GetMemberByMiddleCPFAction $getMemberByMiddleCPFAction,
        GetMemberByCPFAction                   $getMemberByCPFAction,
        UpdateMiddleCpfMemberAction            $updateMiddleCpfMemberAction,
        GetEntryByTimestampValueCpfAction      $getEntryByTimestampValueCpfAction,
        GetEcclesiasticalGroupsFoldersAction   $getEcclesiasticalGroupsFoldersAction,
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
    )
    {
        $this->googleDriveService = $googleDriveService;
        $this->createEntryAction = $createEntryAction;
        $this->getMemberByMiddleCPFAction = $getMemberByMiddleCPFAction;
        $this->getEcclesiasticalGroupsFoldersAction = $getEcclesiasticalGroupsFoldersAction;
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
        $tenants = $this->getTenantsByPlan(PlanRepository::PLAN_GOLD_NAME);

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $this->googleDriveService->defineInstanceGoogleDrive($tenant);
            $this->foldersData = $this->getEcclesiasticalGroupsFoldersAction->__invoke();

            foreach ($this->foldersData as $folderData) {
                $this->processFolder($folderData, $tenant);
            }
        }
    }



    /**
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws Exception
     * @throws BindingResolutionException
     */
    private function processFolder($folderData, $tenant): void
    {
        $this->entryType = $folderData->entry_type;
        $files = $this->googleDriveService->listFiles($folderData->folder_id);

        foreach ($files as $file) {
            $basePathTemp = self::STORAGE_BASE_PATH . "tenants/{$tenant}/temp";
            $this->googleDriveService->deleteFilesInLocalDirectory($basePathTemp);

            $downloadedFile = $this->googleDriveService->download($basePathTemp, $file);
            if (!is_array($downloadedFile)) {
                continue;
            }

            $this->processFile($downloadedFile, $file, $folderData);
        }
    }



    /**
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws BindingResolutionException
     */
    private function processFile($downloadedFile, $file, $folderData): void
    {
        $extractedData = $this->OCRExtractDataBankReceiptService->ocrExtractData($downloadedFile, $this->entryType);

        if (empty($extractedData) || $extractedData['status'] !== 'SUCCESS') {
            return;
        }

        $timestampValueCpf = $extractedData['data']['timestamp_value_cpf'];
        $middleCpf = $extractedData['data']['middle_cpf'];
        $member = $this->findMember($middleCpf);

        if ($this->isDuplicateEntry($timestampValueCpf, $file)) {
            return;
        }

        $this->setEntryData($extractedData, $member, $folderData);

        $entry = $this->createEntryAction->__invoke($this->entryData, $this->consolidationEntriesData);
        $this->updateTimestampValueCPFEntryAction->__invoke($entry->id, $timestampValueCpf);
        //$this->updateIdentificationPendingEntryAction->__invoke($entry->id, self::IDENTIFICATION_PENDING_1);
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

        $member = $this->getMemberByMiddleCPFAction->__invoke($middleCpf);
        if (!$member) {
            $member = $this->getMemberByCPFAction->__invoke($middleCpf);
            if ($member) {
                $this->updateMiddleCpfMemberAction->__invoke($member->id, $middleCpf);
            }
        }

        return $member;
    }



    /**
     * @param string $timestampValueCpf
     * @param $file
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    private function isDuplicateEntry(string $timestampValueCpf, $file): bool
    {
        if (empty($timestampValueCpf)) {
            return false;
        }

        $entry = $this->getEntryByTimestampValueCpfAction->__invoke($timestampValueCpf);
        if ($entry) {
            $this->googleDriveService->renameFile($file->id, null, 'DUPLICATED');
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
        $group = $this->getReturnReceivingGroupAction->__invoke();

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
            '10-12', // Nossa Senhora Aparecida
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
        $plan = $this->getPlanByNameAction->__invoke($planName);

        if(!is_null($plan))
        {
            $tenants = $this->getChurchesByPlanIdAction->__invoke($plan->id);

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
    public function setEntryData(array $extractedData, mixed $member, $folderData): void
    {
        $reviewer = $this->getReviewerAction->__invoke();

        $currentDate = date('Y-m-d');
        $extractedDate = $extractedData['data']['date'];

        $this->entryData->amount = floatval($extractedData['data']['amount']) / 100;
        $this->entryData->comments = 'Entrada registrada automaticamente!';
        $this->entryData->dateEntryRegister = $currentDate;
        $this->entryData->dateTransactionCompensation = $this->getNextBusinessDay($extractedDate) . self::SUFIX_TIMEZONE;
        $this->entryData->deleted = 0;
        $this->entryData->entryType = $folderData->entry_type;
        $this->entryData->memberId = $member?->id;
        $this->entryData->receipt = null;
        $this->entryData->devolution = 0;
        $this->entryData->residualValue = 0;
        $this->entryData->identificationPending = 0;
        $this->entryData->cultId = null;
        $this->entryData->timestampValueCpf = null;

        if($folderData->entry_type == EntryRepository::DESIGNATED_VALUE)
        {
            $this->entryData->groupReceivedId = $folderData->ecclesiastical_divisions_group_id;

            if($folderData->folder_devolution == 1)
            {
                $this->entryData->devolution = 1;
                $this->entryData->groupReceivedId = $this->getReturnReceivingGroup();
                $this->entryData->groupReturnedId = $folderData->ecclesiastical_divisions_group_id;
            }
        }

        $this->entryData->reviewerId = $reviewer->id;
        $this->entryData->transactionCompensation = EntryRepository::COMPENSATED_VALUE;
        $this->entryData->transactionType = EntryRepository::PIX_TRANSACTION_TYPE;

        $this->consolidationEntriesData->date = DateTime::createFromFormat('d/m/Y', $extractedData['data']['date'])->format('Y-m-d');

    }
}
