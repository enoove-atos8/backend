<?php

namespace App\Application\Core\Jobs\Financial\Exits\ReceiptsProcessing;

use App\Domain\Financial\Entries\Entries\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use App\Domain\Financial\Reviewers\DataTransferObjects\FinancialReviewerData;
use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Infrastructure\Repositories\CentralDomain\Church\ChurchRepository;
use App\Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardInstallmentsRepository;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Services\Financial\Interfaces\ReceiptDataExtractorInterface;
use DateTime;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\Exits\Exits\Actions\CreateExitAction;
use Domain\Financial\Exits\Exits\Actions\GetExitByTimestampAction;
use Domain\Financial\Exits\Exits\Actions\UpdateReceiptLinkAction;
use Domain\Financial\Exits\Exits\Actions\UpdateTimestampAction;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Purchases\Actions\GetInvoiceByIdAction;
use Domain\Financial\Exits\Purchases\Actions\UpdateStatusInstallmentAction;
use Domain\Financial\Exits\Purchases\Actions\UpdateStatusInvoiceAction;
use Domain\Financial\ReceiptProcessing\Actions\CreateReceiptProcessing;
use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Domain\Financial\Reviewers\Actions\GetReviewerAction;
use Domain\SyncStorage\Actions\GetSyncStorageDataAction;
use Domain\SyncStorage\Actions\UpdateStatusAction;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardInvoiceRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class ProcessingBankExitsTransferReceipts
{
    // Properties

    protected Collection $syncStorageCollection;

    private GetSyncStorageDataAction $getSyncStorageDataAction;

    private MinioStorageService $minioStorageService;

    private ReceiptDataExtractorInterface $receiptDataExtractor;

    private GetExitByTimestampAction $getExitByTimestampAction;

    private UpdateTimestampAction $updateTimestampAction;

    private UpdateStatusAction $updateStatusAction;

    private CreateExitAction $createExitAction;

    private CreateEntryAction $createEntryAction;

    private UpdateReceiptLinkAction $updateReceiptLinkAction;

    private ExitData $exitData;

    private EntryData $entryData;

    private PaymentCategoryData $paymentCategoryData;

    private PaymentItemData $paymentItemData;

    private GroupData $groupData;

    private DivisionData $divisionData;

    private FinancialReviewerData $financialReviewerData;

    private GetReviewerAction $getReviewerAction;

    private CreateReceiptProcessing $createReceiptProcessing;

    private ReceiptProcessingData $receiptProcessingData;

    private SyncStorageData $syncStorageData;

    private UpdateStatusInvoiceAction $updateStatusInvoiceAction;

    private UpdateStatusInstallmentAction $updateStatusInstallmentAction;

    private GetInvoiceByIdAction $getInvoiceByIdAction;

    // Constants

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';

    const SUFFIX_TIMEZONE = 'T03:00:00.000Z';

    const SHARED_RECEIPTS_FOLDER_NAME = 'shared_receipts';

    const STORED_RECEIPTS_FOLDER_NAME = 'stored_receipts';

    const SYNC_STORAGE_EXITS_ERROR_RECEIPTS = 'sync_storage/financial/error_receipts/exits';

    public function __construct(
        GetSyncStorageDataAction $getSyncStorageDataAction,
        MinioStorageService $minioStorageService,
        ReceiptDataExtractorInterface $receiptDataExtractor,
        GetExitByTimestampAction $getExitByTimestampAction,
        UpdateStatusAction $updateStatusAction,
        CreateExitAction $createExitAction,
        CreateEntryAction $createEntryAction,
        UpdateTimestampAction $updateExitTimestampAction,
        UpdateReceiptLinkAction $updateReceiptLinkAction,
        GetReviewerAction $getReviewerAction,
        ExitData $exitData,
        EntryData $entryData,
        PaymentCategoryData $paymentCategoryData,
        PaymentItemData $paymentItemData,
        GroupData $groupData,
        DivisionData $divisionData,
        FinancialReviewerData $financialReviewerData,
        CreateReceiptProcessing $createReceiptProcessing,
        ReceiptProcessingData $receiptProcessingData,
        SyncStorageData $syncStorageData,
        UpdateStatusInvoiceAction $updateStatusInvoiceAction,
        UpdateStatusInstallmentAction $updateStatusInstallmentAction,
        GetInvoiceByIdAction $getInvoiceByIdAction
    ) {
        $this->getSyncStorageDataAction = $getSyncStorageDataAction;
        $this->minioStorageService = $minioStorageService;
        $this->receiptDataExtractor = $receiptDataExtractor;
        $this->getExitByTimestampAction = $getExitByTimestampAction;
        $this->updateStatusAction = $updateStatusAction;
        $this->createExitAction = $createExitAction;
        $this->createEntryAction = $createEntryAction;
        $this->updateTimestampAction = $updateExitTimestampAction;
        $this->updateReceiptLinkAction = $updateReceiptLinkAction;
        $this->getReviewerAction = $getReviewerAction;
        $this->exitData = $exitData;
        $this->entryData = $entryData;
        $this->paymentCategoryData = $paymentCategoryData;
        $this->paymentItemData = $paymentItemData;
        $this->groupData = $groupData;
        $this->divisionData = $divisionData;
        $this->financialReviewerData = $financialReviewerData;
        $this->createReceiptProcessing = $createReceiptProcessing;
        $this->receiptProcessingData = $receiptProcessingData;
        $this->syncStorageData = $syncStorageData;
        $this->updateStatusInvoiceAction = $updateStatusInvoiceAction;
        $this->updateStatusInstallmentAction = $updateStatusInstallmentAction;
        $this->getInvoiceByIdAction = $getInvoiceByIdAction;
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
        try {
            $tenants = $this->getAllTenants();

            foreach ($tenants as $tenant) {
                tenancy()->initialize($tenant);

                $this->syncStorageCollection = $this->getSyncStorageDataAction->execute(
                    SyncStorageRepository::EXITS_VALUE_DOC_TYPE,
                    null,
                    SyncStorageRepository::PURCHASE_SUB_TYPE_VALUE);

                foreach ($this->syncStorageCollection as $data) {
                    $this->process($data, $tenant);

                    if ($data->creditCardPayment) {
                        $invoiceData = $this->getInvoiceByIdAction->execute($data->invoiceId);
                        $this->updateStatusInvoiceAction->execute($data->invoiceId, CardInvoiceRepository::INVOICE_PAID_VALUE);
                        $this->updateStatusInstallmentAction->execute($data->invoiceId, $invoiceData->referenceDate, CardInstallmentsRepository::PAID_VALUE);
                    }
                }
            }
        } catch (GeneralExceptions $e) {
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
        $basePathTemp = self::STORAGE_BASE_PATH."tenants/{$tenant}/temp";
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
        $extractedData = $this->receiptDataExtractor->extractData($downloadedFile, $syncStorageData->docType, $syncStorageData->docSubType);

        if (count($extractedData) > 0 && $extractedData['status'] == 'SUCCESS') {
            $timestamp = $extractedData['data']['timestamp_value_cpf'];

            if ($timestamp != '') {
                // Verifica se o timestamp contém 000000 (sem hora real do comprovante)
                // Se contém 000000, significa que não há hora/minuto/segundo no comprovante
                // Nesse caso, permite o cadastro e deixa para análise manual de duplicidades
                $hasNoRealTime = str_contains($timestamp, '000000_');

                if (!$hasNoRealTime && $this->isDuplicateExit($timestamp)) {
                    $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::DUPLICATED_RECEIPT_VALUE);
                    $this->minioStorageService->delete($syncStorageData->path, $tenant);

                    return;
                }
            }

            $this->setExitData($extractedData, $syncStorageData);

            $exit = $this->createExitAction->execute($this->exitData);

            if ($timestamp != '') {
                $this->updateTimestampAction->execute($exit->id, $timestamp);
            }

            $sharedPath = $syncStorageData->path;
            $syncStorageData->path = str_replace(self::SHARED_RECEIPTS_FOLDER_NAME, self::STORED_RECEIPTS_FOLDER_NAME, $syncStorageData->path);
            $urlParts = explode('/', $syncStorageData->path);
            array_pop($urlParts);
            $path = implode('/', $urlParts);
            $fileUrl = $this->minioStorageService->upload($downloadedFile['fileUploaded'], $path, $tenant);

            if (! empty($fileUrl)) {
                $this->minioStorageService->delete($sharedPath, $tenant);
            }

            $this->updateReceiptLinkAction->execute($exit->id, $fileUrl);
            $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::DONE_VALUE);

            if ($syncStorageData->docSubType == ExitRepository::ACCOUNTS_TRANSFER_VALUE) {
                $this->setEntryDataFromTransfer($extractedData, $fileUrl, $syncStorageData);
                $this->createEntryAction->execute($this->entryData, null);
            }

        } elseif (count($extractedData) > 0 && $extractedData['status'] != 'SUCCESS') {
            $linkReceipt = $this->minioStorageService->upload($downloadedFile['fileUploaded'], self::SYNC_STORAGE_EXITS_ERROR_RECEIPTS, $tenant, true);

            if (! empty($linkReceipt)) {
                $this->minioStorageService->delete($syncStorageData->path, $tenant);
            }

            $this->setReceiptProcessingData($extractedData, $syncStorageData, $linkReceipt);
            $this->createReceiptProcessing->execute($this->receiptProcessingData);
            $this->updateStatusAction->execute($syncStorageData->id, SyncStorageRepository::ERROR_VALUE);
        }
    }

    /**
     * @param  mixed  $data
     *
     * @throws Throwable
     */
    public function setReceiptProcessingData($extractedData, SyncStorageData $data, string $linkReceipt): void
    {
        $reviewer = $this->getReviewerAction->execute();

        $this->receiptProcessingData = ReceiptProcessingData::fromExtractedData(
            $data, $extractedData, $linkReceipt, $reviewer
        );
    }

    /**
     * Get all active tenants regardless of plan
     */
    public function getAllTenants(): array
    {
        return tenancy()->central(function () {
            return DB::table(ChurchRepository::TABLE_NAME)
                ->where(ChurchRepository::ACTIVATED_COLUMN, true)
                ->pluck(ChurchRepository::TENANT_ID_COLUMN)
                ->toArray();
        });
    }

    /**
     * @throws Throwable
     */
    private function isDuplicateExit(string $timestamp): bool
    {
        if (empty($timestamp)) {
            return false;
        }

        $exit = $this->getExitByTimestampAction->execute($timestamp);

        if ($exit) {
            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function setExitData(array $extractedData, SyncStorageData $data): void
    {
        $reviewer = $this->getReviewerAction->execute();
        $nextBusinessDay = $this->getNextBusinessDay($extractedData['data']['date']);

        $this->exitData = ExitData::fromExtractedData(
            $extractedData,
            $data,
            $reviewer,
            $nextBusinessDay
        );

    }

    /**
     * @throws Exception
     */
    public function getNextBusinessDay($date): string
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
            do {
                $currentDate->modify('+1 day');
                $dayOfWeek = $currentDate->format('N');
                $monthDay = $currentDate->format('m-d');
            } while (in_array($dayOfWeek, [6, 7]) || in_array($monthDay, $holidays));

            return $currentDate->format('Y-m-d');
        }

        return $currentDate->format('Y-m-d');
    }

    /**
     * Cria EntryData a partir de dados de transferência entre contas
     *
     * @throws Exception
     * @throws Throwable
     */
    public function setEntryDataFromTransfer(array $extractedData, string $linkReceipt, SyncStorageData $data): void
    {
        $reviewer = $this->getReviewerAction->execute();
        $currentDate = date('Y-m-d');
        $extractedDate = $extractedData['data']['date'];
        $nextBusinessDay = $this->getNextBusinessDay($extractedDate);

        $dateTransactionCompensation = $nextBusinessDay.'T03:00:00.000Z';

        $this->entryData = new EntryData([
            'id' => null,
            'amount' => floatval($extractedData['data']['amount']) / 100,
            'comments' => 'Entrada por transferência entre contas',
            'dateEntryRegister' => $currentDate,
            'dateTransactionCompensation' => $dateTransactionCompensation,
            'deleted' => 0,
            'entryType' => EntryRepository::ACCOUNTS_TRANSFER_VALUE,
            'memberId' => null,
            'accountId' => $data->destinationAccountId, // Conta destino
            'receipt' => $linkReceipt,
            'devolution' => 0,
            'residualValue' => 0,
            'identificationPending' => 0,
            'cultId' => null,
            'timestampValueCpf' => null,
            'reviewerId' => $reviewer->id,
            'transactionCompensation' => EntryRepository::COMPENSATED_VALUE,
            'transactionType' => EntryRepository::PIX_TRANSACTION_TYPE,
            'groupReceivedId' => null,
            'groupReturnedId' => null,
        ]);
    }
}
