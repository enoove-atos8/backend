<?php

namespace Application\Core\Jobs\Financial\Accounts;

use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Balances\CalculateAccountBalanceAction;
use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Balances\SaveOrUpdateBalanceAction;
use App\Domain\Financial\AccountsAndCards\Accounts\Constants\Movements\ReturnMessages;
use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Carbon\Carbon;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Files\ChangeFileProcessingStatusAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Files\GetLastProcessedFileAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountByIdAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Movements\CreateBulkMovementsAction;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\BankStatementExtractorFactory;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class ProcessAccountFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';

    const RELATIVE_LOCAL_PATH = '/accounts/files/temp';

    const S3_ACCOUNTS_FILES_PATH = '/financial/accounts/files';

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int $fileId,
        private readonly string $processingType,
        private readonly string $tenant
    ) {}

    /**
     * Execute the job.
     *
     * @throws GeneralExceptions|TenantCouldNotBeIdentifiedById|\Throwable
     */
    public function handle(
        AccountFilesRepository $accountFilesRepository,
        MinioStorageService $minioStorageService,
        BankStatementExtractorFactory $extractorFactory,
        CreateBulkMovementsAction $createBulkMovementsAction,
        ChangeFileProcessingStatusAction $changeFileProcessingStatusAction,
        GetAccountByIdAction $getAccountByIdAction,
        CalculateAccountBalanceAction $calculateAccountBalanceAction,
        SaveOrUpdateBalanceAction $saveOrUpdateBalanceAction,
        GetLastProcessedFileAction $getLastProcessedFileAction
    ): void {
        tenancy()->initialize($this->tenant);

        try {
            $file = $accountFilesRepository->getFilesById($this->fileId);

            // Validar processamento sequencial antes de processar o arquivo
            $this->validateSequentialProcessing($file, $getAccountByIdAction, $getLastProcessedFileAction);

            $downloadFile = $this->downloadFile($file->link, $minioStorageService);
            $extractedData = $this->dataExtraction($downloadFile, $this->processingType, $file, $extractorFactory);

            if (! empty($extractedData)) {
                $movements = collect($extractedData);
                $inserted = $createBulkMovementsAction->execute($movements, $file->accountId, $this->fileId, $file->referenceDate);

                if (! $inserted) {
                    throw new GeneralExceptions(ReturnMessages::INSERT_BULK_MOVEMENTS_ERROR, 500);
                }

                // Calcular e persistir saldos após inserir as movimentações com sucesso
                $this->calculateAndSaveBalance(
                    $file->accountId,
                    $file->referenceDate,
                    $calculateAccountBalanceAction,
                    $saveOrUpdateBalanceAction
                );

                $status = $this->processingType == AccountFilesRepository::TYPE_PROCESSING_MOVEMENTS_EXTRACTION
                    ? AccountFilesRepository::MOVEMENTS_DONE
                    : AccountFilesRepository::CONCILIATION_DONE;

                $changeFileProcessingStatusAction->execute($this->fileId, $status);
            }
        } catch (Exception $e) {
            $errorCode = $e->getCode();

            if ($errorCode == 422) {
                $changeFileProcessingStatusAction->execute($this->fileId, AccountFilesRepository::DIFFERENT_ACCOUNT_FILE);

                return;
            } elseif ($errorCode == 423) {
                $changeFileProcessingStatusAction->execute($this->fileId, AccountFilesRepository::DIFFERENT_MONTH_FILE);

                return;
            } elseif ($errorCode == 424) {
                $changeFileProcessingStatusAction->execute($this->fileId, AccountFilesRepository::MOVEMENTS_ERROR);
                throw new GeneralExceptions($e->getMessage(), 424);
            } else {
                $status = $this->processingType == AccountFilesRepository::TYPE_PROCESSING_MOVEMENTS_EXTRACTION
                    ? AccountFilesRepository::MOVEMENTS_ERROR
                    : AccountFilesRepository::CONCILIATION_ERROR;

                $changeFileProcessingStatusAction->execute($this->fileId, $status);

                throw new GeneralExceptions($e->getMessage(), 500);
            }
        }
    }

    /**
     * Download file from remote storage to local storage
     *
     * @throws GeneralExceptions
     */
    private function downloadFile(string $fileLink, MinioStorageService $minioStorageService): string
    {
        $absoluteS3Path = self::S3_ACCOUNTS_FILES_PATH.'/'.basename($fileLink);
        $basePathTemp = self::STORAGE_BASE_PATH.'tenants/'.$this->tenant.self::RELATIVE_LOCAL_PATH;

        $minioStorageService->deleteFilesInLocalDirectory($basePathTemp);

        return $minioStorageService->downloadFileOnly($absoluteS3Path, $this->tenant, $basePathTemp);
    }

    /**
     * Extract data from downloaded file based on processing type and file format
     *
     * @throws GeneralExceptions
     */
    private function dataExtraction(
        string $fileDownloaded,
        string $processingType,
        AccountFileData $file,
        BankStatementExtractorFactory $extractorFactory
    ): ?array {
        if ($processingType == AccountFilesRepository::TYPE_PROCESSING_MOVEMENTS_EXTRACTION) {
            $fileBaseName = Str::lower(basename($fileDownloaded));
            $isTxtFile = Str::contains($fileBaseName, Str::lower(AccountFilesRepository::TXT_TYPE_EXTRACTION));
            $isPdfFile = Str::contains($fileBaseName, Str::lower(AccountFilesRepository::PDF_TYPE_EXTRACTION));

            if ($isTxtFile || $isPdfFile) {
                $bankName = Str::lower(Str::ascii($file->account->bankName));
                $extractor = $extractorFactory->make($bankName);

                return $extractor->extract($fileDownloaded, $file);
            }
        }

        if ($processingType == AccountFilesRepository::TYPE_PROCESSING_BANK_CONCILIATION) {
            if (Str::contains(basename($fileDownloaded), Str::lower(AccountFilesRepository::OFX_TYPE_EXTRACTION))) {
                // TODO: Implement OFX extraction logic

            }
        }

        return null;
    }

    /**
     * Validate that files are being processed in sequential order
     * Users must process bank statements month by month without skipping
     *
     * @throws GeneralExceptions
     * @throws \Throwable
     */
    private function validateSequentialProcessing(
        AccountFileData $file,
        GetAccountByIdAction $getAccountByIdAction,
        GetLastProcessedFileAction $getLastProcessedFileAction
    ): void {
        $currentReferenceDate = $file->referenceDate;

        // Buscar o último arquivo processado com sucesso
        $lastProcessedFile = $getLastProcessedFileAction->execute($file->accountId);

        // Se não existe nenhum arquivo processado, permite qualquer mês (primeiro processamento)
        if (! $lastProcessedFile) {
            return;
        }

        // Se já existe arquivo processado, validar processamento sequencial
        // Calcular o próximo mês esperado após o último processado
        $nextExpectedMonth = Carbon::createFromFormat('Y-m', $lastProcessedFile->referenceDate)
            ->addMonth()
            ->format('Y-m');

        // O arquivo atual pode ser:
        // - O próximo mês sequencial (novo processamento)
        // - O mesmo mês do último processado (reprocessamento)
        $isNextMonth = $currentReferenceDate === $nextExpectedMonth;
        $isReprocessing = $currentReferenceDate === $lastProcessedFile->referenceDate;

        if (! $isNextMonth && ! $isReprocessing) {
            $lastMonthFormatted = Carbon::createFromFormat('Y-m', $lastProcessedFile->referenceDate)->format('m/Y');
            $currentMonthFormatted = Carbon::createFromFormat('Y-m', $currentReferenceDate)->format('m/Y');
            $nextMonthFormatted = Carbon::createFromFormat('Y-m', $nextExpectedMonth)->format('m/Y');

            throw new GeneralExceptions(
                sprintf(
                    ReturnMessages::SEQUENTIAL_PROCESSING_ERROR,
                    $currentMonthFormatted,
                    $lastMonthFormatted,
                    $nextMonthFormatted,
                    $lastMonthFormatted
                ),
                424
            );
        }
    }

    /**
     * Calculate and save account balance for the processed month
     *
     * @throws \Throwable
     */
    private function calculateAndSaveBalance(
        int $accountId,
        string $referenceDate,
        CalculateAccountBalanceAction $calculateAccountBalanceAction,
        SaveOrUpdateBalanceAction $saveOrUpdateBalanceAction
    ): void {
        // Calcular saldos do mês
        $balanceData = $calculateAccountBalanceAction->execute($accountId, $referenceDate);

        // Persistir na tabela accounts_balances
        $saveOrUpdateBalanceAction->execute($balanceData);
    }
}
