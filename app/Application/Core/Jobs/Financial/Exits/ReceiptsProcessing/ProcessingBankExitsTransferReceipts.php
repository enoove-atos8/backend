<?php

namespace App\Application\Core\Jobs\Financial\Exits\ReceiptsProcessing;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use App\Domain\Financial\Reviewers\DataTransferObjects\FinancialReviewerData;
use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Infrastructure\Services\Atos8\Financial\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
use DateTime;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesByPlanIdAction;
use Domain\CentralDomain\Plans\Actions\GetPlanByNameAction;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\Exits\Exits\Actions\CreateExitAction;
use Domain\Financial\Exits\Exits\Actions\GetExitByTimestampAction;
use Domain\Financial\Exits\Exits\Actions\UpdateReceiptLinkAction;
use Domain\Financial\Exits\Exits\Actions\UpdateTimestampAction;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\ReceiptProcessing\Actions\CreateReceiptProcessing;
use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Domain\Financial\Reviewers\Actions\GetReviewerAction;
use Domain\SyncStorage\Actions\GetSyncStorageDataAction;
use Domain\SyncStorage\Actions\UpdateStatusAction;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PlanRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class ProcessingBankExitsTransferReceipts
{
    //Properties

    private GetPlanByNameAction $getPlanByNameAction;
    protected Collection $syncStorageData;
    private GetSyncStorageDataAction $getSyncStorageDataAction;
    private MinioStorageService $minioStorageService;
    private GetChurchesByPlanIdAction $getChurchesByPlanIdAction;
    private OCRExtractDataBankReceiptService $OCRExtractDataBankReceiptService;
    private GetExitByTimestampAction $getExitByTimestampAction;
    private UpdateTimestampAction $updateTimestampAction;
    private UpdateStatusAction $updateStatusAction;
    private CreateExitAction $createExitAction;
    private UpdateReceiptLinkAction    $updateReceiptLinkAction;
    private ExitData $exitData;
    private PaymentCategoryData $paymentCategoryData;
    private PaymentItemData $paymentItemData;
    private GroupData $groupData;
    private DivisionData $divisionData;
    private FinancialReviewerData $financialReviewerData;
    private GetReviewerAction $getReviewerAction;
    private CreateReceiptProcessing $createReceiptProcessing;
    private ReceiptProcessingData $receiptProcessingData;
    private



    //Constants

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';
    const SUFFIX_TIMEZONE = 'T03:00:00.000Z';
    const SHARED_RECEIPTS_FOLDER_NAME = 'shared_receipts';
    const STORED_RECEIPTS_FOLDER_NAME = 'stored_receipts';
    const SYNC_STORAGE_EXITS_ERROR_RECEIPTS = 'sync_storage/financial/error_receipts/exits';

    public function __construct(
        GetPlanByNameAction              $getPlanByNameAction,
        GetSyncStorageDataAction         $getSyncStorageDataAction,
        MinioStorageService              $minioStorageService,
        GetChurchesByPlanIdAction        $getChurchesByPlanIdAction,
        OCRExtractDataBankReceiptService $OCRExtractDataBankReceiptService,
        GetExitByTimestampAction         $getExitByTimestampAction,
        UpdateStatusAction               $updateStatusAction,
        CreateExitAction                 $createExitAction,
        UpdateTimestampAction            $updateExitTimestampAction,
        UpdateReceiptLinkAction          $updateReceiptLinkAction,
        GetReviewerAction                $getReviewerAction,
        ExitData                         $exitData,
        PaymentCategoryData              $paymentCategoryData,
        PaymentItemData                  $paymentItemData,
        GroupData                        $groupData,
        DivisionData                     $divisionData,
        FinancialReviewerData            $financialReviewerData,
        CreateReceiptProcessing          $createReceiptProcessing,
        ReceiptProcessingData            $receiptProcessingData
    )
    {
        $this->getPlanByNameAction = $getPlanByNameAction;
        $this->getSyncStorageDataAction = $getSyncStorageDataAction;
        $this->minioStorageService = $minioStorageService;
        $this->getChurchesByPlanIdAction = $getChurchesByPlanIdAction;
        $this->OCRExtractDataBankReceiptService = $OCRExtractDataBankReceiptService;
        $this->getExitByTimestampAction = $getExitByTimestampAction;
        $this->updateStatusAction = $updateStatusAction;
        $this->createExitAction = $createExitAction;
        $this->updateTimestampAction = $updateExitTimestampAction;
        $this->updateReceiptLinkAction = $updateReceiptLinkAction;
        $this->getReviewerAction = $getReviewerAction;
        $this->exitData = $exitData;
        $this->paymentCategoryData = $paymentCategoryData;
        $this->paymentItemData = $paymentItemData;
        $this->groupData = $groupData;
        $this->divisionData = $divisionData;
        $this->financialReviewerData = $financialReviewerData;
        $this->createReceiptProcessing = $createReceiptProcessing;
        $this->receiptProcessingData = $receiptProcessingData;
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

                $this->syncStorageData = $this->getSyncStorageDataAction->execute(SyncStorageRepository::EXITS_VALUE_DOC_TYPE);

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
     * @throws Throwable
     */
    private function processFile(array $downloadedFile, SyncStorageData $syncStorageData, string $tenant): void
    {
        $extractedData = $this->OCRExtractDataBankReceiptService->ocrExtractData($downloadedFile, $syncStorageData->docType, $syncStorageData->docSubType);

        if (count($extractedData) > 0 && $extractedData['status'] == 'SUCCESS')
        {
            $timestamp = $extractedData['data']['timestamp_value_cpf'];

            if($timestamp != '')
            {
                if ($this->isDuplicateExit($timestamp)){
                    $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::DUPLICATED_RECEIPT_VALUE);
                    $this->minioStorageService->delete($syncStorageData->path, $tenant);
                    return;
                }
            }


            $this->setExitData($extractedData, $syncStorageData);

            $exit = $this->createExitAction->execute($this->exitData);

            if($timestamp != '')
                $this->updateTimestampAction->execute($exit->id, $timestamp);

            $sharedPath = $syncStorageData->path;
            $syncStorageData->path = str_replace(self::SHARED_RECEIPTS_FOLDER_NAME, self::STORED_RECEIPTS_FOLDER_NAME, $syncStorageData->path);
            $urlParts = explode('/', $syncStorageData->path);
            array_pop($urlParts);
            $path = implode('/', $urlParts);
            $fileUrl = $this->minioStorageService->upload($downloadedFile['fileUploaded'], $path, $tenant);

            if(!empty($fileUrl))
                $this->minioStorageService->delete($sharedPath, $tenant);

            $this->updateReceiptLinkAction->execute($exit->id, $fileUrl);
            $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::DONE_VALUE);

        }
        else if(count($extractedData) > 0 && $extractedData['status'] != 'SUCCESS')
        {
            $linkReceipt = $this->minioStorageService->upload($downloadedFile['fileUploaded'], self::SYNC_STORAGE_EXITS_ERROR_RECEIPTS, $tenant, true);

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
        $this->receiptProcessingData->docType = ExitRepository::EXITS_VALUE;
        $this->receiptProcessingData->docSubType = $data->docSubType;
        $this->receiptProcessingData->amount = floatval($extractedData['data']['amount']) != 0 ? $extractedData['data']['amount'] : 0;
        $this->receiptProcessingData->reason = $extractedData['status'];
        $this->receiptProcessingData->status = 'error';
        $this->receiptProcessingData->institution = $extractedData['data']['institution'] != '' ? $extractedData['data']['institution'] : null;
        $this->receiptProcessingData->devolution = $data->isDevolution == 1;
        $this->receiptProcessingData->isPayment = false;
        $this->receiptProcessingData->deleted = false;
        $this->receiptProcessingData->receiptLink = $linkReceipt;
        $this->receiptProcessingData->division = new DivisionData(['id' => !is_null($data->divisionId) ? (int) $data->divisionId : null]);
        $this->receiptProcessingData->groupReceived = new GroupData(['id' => !is_null($data->groupId) ? (int) $data->groupId : null]);
        $this->receiptProcessingData->groupReturned = new GroupData(['id' => null]);
        $this->receiptProcessingData->paymentCategory = new PaymentCategoryData(['id' => null]);
        $this->receiptProcessingData->paymentItem = new PaymentItemData(['id' => null]);

        if($data->isPayment)
        {
            $this->receiptProcessingData->paymentCategory = new PaymentCategoryData(['id' => $data->paymentCategoryId]);
            $this->receiptProcessingData->paymentItem = new PaymentItemData(['id' => $data->paymentItemId]);
        }
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
     * @param string $timestamp
     * @return bool
     * @throws Throwable
     */
    private function isDuplicateExit(string $timestamp): bool
    {
        if (empty($timestamp))
            return false;

        $exit = $this->getExitByTimestampAction->execute($timestamp);

        if ($exit)
            return true;

        return false;
    }



    /**
     *
     * @throws Exception
     * @throws Throwable
     */
    public function setExitData(array $extractedData, SyncStorageData $data): void
    {
        $reviewer = $this->getReviewerAction->execute();

        $currentDate = date('Y-m-d');
        $extractedDate = $extractedData['data']['date'];

        $this->exitData->amount = floatval($extractedData['data']['amount']) / 100;
        $this->exitData->comments = 'Saída registrada automaticamente!';
        $this->exitData->dateExitRegister = $currentDate;
        $this->exitData->dateTransactionCompensation = $this->getNextBusinessDay($extractedDate) . self::SUFFIX_TIMEZONE;
        $this->exitData->deleted = 0;
        $this->exitData->exitType = $data->docSubType;
        $this->exitData->receiptLink = '';
        $this->exitData->timestampExitTransaction = null;

        if($data->docSubType == ExitRepository::PAYMENTS_VALUE)
        {
            $this->exitData->isPayment = 1;
            $this->exitData->paymentItem = new PaymentItemData(['id' => $data->paymentItemId]);
            $this->exitData->paymentCategory = new PaymentCategoryData(['id' => $data->paymentCategoryId]);
            $this->exitData->group = new GroupData(['id' => null]);
            $this->exitData->division = new DivisionData(['id' => null]);
        }

        if($data->docSubType != ExitRepository::PAYMENTS_VALUE)
        {
            $this->exitData->isPayment = 0;
            $this->exitData->paymentItem = new PaymentItemData(['id' => null]);
            $this->exitData->paymentCategory = new PaymentCategoryData(['id' => null]);
            $this->exitData->group = new GroupData(['id' => $data->groupId]);
            $this->exitData->division = new DivisionData(['id' => $data->divisionId]);
        }


        $this->exitData->financialReviewer = new FinancialReviewerData(['id' => $reviewer->id]);
        $this->exitData->transactionCompensation = ExitRepository::COMPENSATED_VALUE;
        $this->exitData->transactionType = $data->docSubType == ExitRepository::PAYMENTS_VALUE ? ExitRepository::BANK_SLIP_VALUE : ExitRepository::PIX_VALUE;

    }



    /**
     *
     * @throws Exception
     */
    function getNextBusinessDay($date): string
    {
        $holidays = [
            '01-01',
            '04-21',
            '05-01',
            '05-30',
            '09-07',
            '10-12',
            '11-02',
            '11-15',
            '12-25',
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
}
