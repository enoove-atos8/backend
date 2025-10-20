<?php

namespace Application\Core\Jobs\Financial\Accounts;

use App\Domain\Financial\AccountsAndCards\Accounts\Constants\Movements\ReturnMessages;
use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Files\ChangeFileProcessingStatusAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Movements\CreateBulkMovementsAction;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\BankStatementExtractorFactory;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
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
        private readonly int    $fileId,
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
        ChangeFileProcessingStatusAction $changeFileProcessingStatusAction
    ): void
    {
        tenancy()->initialize($this->tenant);

        try
        {
            $file = $accountFilesRepository->getFilesById($this->fileId);
            $downloadFile = $this->downloadFile($file->link, $minioStorageService);
            $extractedData = $this->dataExtraction($downloadFile, $this->processingType, $file, $extractorFactory);

            if (!empty($extractedData)) {
                $movements = collect($extractedData);
                $inserted = $createBulkMovementsAction->execute($movements, $file->accountId, $this->fileId, $file->referenceDate);

                if (!$inserted) {
                    throw new GeneralExceptions(ReturnMessages::INSERT_BULK_MOVEMENTS_ERROR, 500);
                }

                $status = $this->processingType == AccountFilesRepository::TYPE_PROCESSING_MOVEMENTS_EXTRACTION
                    ? AccountFilesRepository::MOVEMENTS_DONE
                    : AccountFilesRepository::CONCILIATION_DONE;

                $changeFileProcessingStatusAction->execute($this->fileId, $status);
            }
        }
        catch (Exception $e) {
            $errorCode = $e->getCode();

            if ($errorCode == 422)
            {
                $changeFileProcessingStatusAction->execute($this->fileId, AccountFilesRepository::DIFFERENT_ACCOUNT_FILE);
                return;
            }
            elseif ($errorCode == 423)
            {
                $changeFileProcessingStatusAction->execute($this->fileId, AccountFilesRepository::DIFFERENT_MONTH_FILE);
                return;
            }
            else
            {
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
     * @param string $fileLink
     * @param MinioStorageService $minioStorageService
     * @return string
     * @throws GeneralExceptions
     */
    private function downloadFile(string $fileLink, MinioStorageService $minioStorageService): string
    {
        $absoluteS3Path = self::S3_ACCOUNTS_FILES_PATH . '/' . basename($fileLink);
        $basePathTemp = self::STORAGE_BASE_PATH . 'tenants/' . $this->tenant . self::RELATIVE_LOCAL_PATH;

        $minioStorageService->deleteFilesInLocalDirectory($basePathTemp);

        return $minioStorageService->downloadFileOnly($absoluteS3Path, $this->tenant, $basePathTemp);
    }




    /**
     * Extract data from downloaded file based on processing type and file format
     *
     * @param string $fileDownloaded
     * @param string $processingType
     * @param AccountFileData $file
     * @param BankStatementExtractorFactory $extractorFactory
     * @return array|null
     * @throws GeneralExceptions
     */
    private function dataExtraction(
        string $fileDownloaded,
        string $processingType,
        AccountFileData $file,
        BankStatementExtractorFactory $extractorFactory
    ): ?array
    {
        if($processingType == AccountFilesRepository::TYPE_PROCESSING_MOVEMENTS_EXTRACTION)
        {
            if(Str::contains(basename($fileDownloaded), Str::lower(AccountFilesRepository::TXT_TYPE_EXTRACTION)))
            {
                $bankName = Str::lower(Str::ascii($file->account->bankName));
                $extractor = $extractorFactory->make($bankName);
                return $extractor->extract($fileDownloaded, $file);
            }
        }

        if($processingType == AccountFilesRepository::TYPE_PROCESSING_BANK_CONCILIATION)
        {
            if(Str::contains(basename($fileDownloaded), Str::lower(AccountFilesRepository::OFX_TYPE_EXTRACTION)))
            {
                // TODO: Implement OFX extraction logic

            }
        }

        return null;
    }
}
