<?php

namespace App\Application\Core\Jobs;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\General\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\General\Actions\GetEntryByTimestampValueCpfAction;
use App\Domain\Financial\Entries\General\Actions\UpdateIdentificationPendingEntryAction;
use App\Domain\Financial\Entries\General\Actions\UpdateReceiptLinkEntryAction;
use App\Domain\Financial\Entries\General\Actions\UpdateTimestampValueCPFEntryAction;
use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use App\Domain\Members\Actions\GetMemberByCPFAction;
use App\Domain\Members\Actions\GetMemberByMiddleCPFAction;
use App\Domain\Members\Actions\UpdateMiddleCpfMemberAction;
use DateTime;
use Domain\Ecclesiastical\Folders\Actions\GetEcclesiasticalGroupsFoldersAction;
use Domain\Ecclesiastical\Folders\DataTransferObjects\FolderData;
use Domain\Financial\Receipts\Entries\Unidentified\Actions\CreateUnidentifiedReceiptAction;
use Domain\Financial\Receipts\Entries\Unidentified\DataTransferObjects\UnidentifiedReceiptData;
use Domain\Members\DataTransferObjects\MemberData;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Exception;
use Google\Service\Drive\DriveFile;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\GoogleDrive\GoogleDriveService;
use Infrastructure\Services\GoogleSheets\GoogleSheetsService;
use Infrastructure\Services\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
use Infrastructure\Util\Storage\S3\UploadFile;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use thiagoalessio\TesseractOCR\TesseractOcrException;
use Throwable;

class ProcessingEntriesByCollectionWorship
{
    private GoogleDriveService $googleDriveService;
    private GoogleSheetsService $googleSheetsService;
    private CreateEntryAction $createEntryAction;
    private GetMemberByMiddleCPFAction $getMemberByMiddleCPFAction;
    private GetEntryByTimestampValueCpfAction $getEntryByTimestampValueCpfAction;
    private GetEcclesiasticalGroupsFoldersAction $getEcclesiasticalGroupsFoldersAction;
    private UpdateMiddleCpfMemberAction $updateMiddleCpfMemberAction;
    private OCRExtractDataBankReceiptService $OCRExtractDataBankReceiptService;
    private UpdateIdentificationPendingEntryAction $updateIdentificationPendingEntryAction;
    private UpdateReceiptLinkEntryAction $updateReceiptLinkEntryAction;
    private UpdateTimestampValueCPFEntryAction $updateTimestampValueCPFEntryAction;
    private CreateUnidentifiedReceiptAction $createUnidentifiedReceiptAction;
    private GetMemberByCPFAction $getMemberByCPFAction;
    private UploadFile $uploadFile;
    private ConsolidationEntriesData $consolidationEntriesData;
    private EntryData $entryData;
    private UnidentifiedReceiptData $unidentifiedReceiptData;
    private  MemberData $memberData;
    private string $entryType;
    private array $entriesBlock = [
        'tithe'         =>  null,
        'designated'    =>  null,
        'offers'        =>  null,
    ];

    private array $allowedTenants = [
        'iebrd'
    ];

    private array $arrReceiptsFounded = [];
    protected Collection $foldersData;
    protected Drive $drive;
    protected Client $client;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';
    const IDENTIFICATION_PENDING_1 = 1;
    const IDENTIFICATION_PENDING_0 = 0;
    const S3_ENTRIES_RECEIPT_PATH = 'entries/assets/receipts';
    const S3_ENTRIES_RECEIPT_UNIDENTIFIED_PATH = 'entries/assets/receipts/unidentified';
    const SUFIX_TIMEZONE = 'T03:00:00.000Z';


    public function __construct(
        GoogleDriveService $googleDriveService,
        GoogleSheetsService $googleSheetsService,
        CreateEntryAction $createEntryAction,
        GetMemberByMiddleCPFAction $getMemberByMiddleCPFAction,
        GetMemberByCPFAction $getMemberByCPFAction,
        UpdateMiddleCpfMemberAction $updateMiddleCpfMemberAction,
        GetEntryByTimestampValueCpfAction $getEntryByTimestampValueCpfAction,
        GetEcclesiasticalGroupsFoldersAction $getEcclesiasticalGroupsFoldersAction,
        UploadFile $uploadFile,
        UpdateIdentificationPendingEntryAction $updateIdentificationPendingEntryAction,
        UpdateReceiptLinkEntryAction $updateReceiptLinkEntryAction,
        UpdateTimestampValueCPFEntryAction $updateTimestampValueCPFEntryAction,
        EntryData $entryData,
        UnidentifiedReceiptData $unidentifiedReceiptData,
        MemberData $memberData,
        OCRExtractDataBankReceiptService $OCRExtractDataBankReceiptService,
        ConsolidationEntriesData $consolidationEntriesData,
        CreateUnidentifiedReceiptAction $createUnidentifiedReceiptAction,
    )
    {
        $this->googleDriveService = $googleDriveService;
        $this->googleSheetsService = $googleSheetsService;
        $this->createEntryAction = $createEntryAction;
        $this->getMemberByMiddleCPFAction = $getMemberByMiddleCPFAction;
        $this->getEcclesiasticalGroupsFoldersAction = $getEcclesiasticalGroupsFoldersAction;
        $this->updateMiddleCpfMemberAction = $updateMiddleCpfMemberAction;
        $this->updateIdentificationPendingEntryAction = $updateIdentificationPendingEntryAction;
        $this->updateReceiptLinkEntryAction = $updateReceiptLinkEntryAction;
        $this->updateTimestampValueCPFEntryAction = $updateTimestampValueCPFEntryAction;
        $this->getEntryByTimestampValueCpfAction = $getEntryByTimestampValueCpfAction;
        $this->getMemberByCPFAction = $getMemberByCPFAction;
        $this->unidentifiedReceiptData = $unidentifiedReceiptData;
        $this->uploadFile = $uploadFile;
        $this->entryData = $entryData;
        $this->memberData = $memberData;
        $this->consolidationEntriesData = $consolidationEntriesData;
        $this->OCRExtractDataBankReceiptService = $OCRExtractDataBankReceiptService;
        $this->createUnidentifiedReceiptAction = $createUnidentifiedReceiptAction;
    }


    /**
     * @return void
     * @throws Exception
     * @throws TesseractOcrException
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Throwable
     */
    public function handle(): void
    {
        foreach ($this->allowedTenants as $tenant)
        {
            tenancy()->initialize($tenant);

            $this->drive = $this->googleDriveService->defineInstanceGoogleDrive($tenant);
            $this->client = $this->googleDriveService->getInstanceGoogleClient($tenant);

            $this->foldersData = $this->getEcclesiasticalGroupsFoldersAction->__invoke(true);

            foreach ($this->foldersData as $key => $folderData)
            {
                $files = $this->googleDriveService->listFiles($folderData->folder_id);

                foreach ($files as $file)
                {
                    $depositDate = $this->googleSheetsService->getDepositDate($this->client, $file->id);
                    $linkReceipts = $this->getReceiptByAmount($tenant, $file->id, $depositDate[0][0]);
                    $entry = null;

                    $this->entriesBlock['tithe'] = $this->googleSheetsService->readTithesBlock($this->client, $file->id);
                    $this->entriesBlock['designated'] = $this->googleSheetsService->readDesignatedBlock($this->client, $file->id);
                    $this->entriesBlock['offers'] = $this->googleSheetsService->readOffersBlock($this->client, $file->id);

                    foreach ($this->entriesBlock as $key => $entries)
                    {
                        if(count($entries) != 0)
                        {
                            $this->setEntryData(array_values($entries), $key, $depositDate);
                            $entry = $this->createEntryAction->__invoke($this->entryData, $this->consolidationEntriesData);

                            if(count($linkReceipts) == 1)
                                $this->updateReceiptLinkEntryAction->__invoke($entry->id, $linkReceipts[0]);

                            else if (count($linkReceipts) > 1)
                                $this->updateReceiptLinkEntryAction->__invoke($entry->id, json_encode($linkReceipts));
                        }
                    }
                }
            }
        }
    }


    /**
     * @throws Throwable
     * @throws TesseractOcrException
     * @throws Exception
     */
    public function getReceiptByAmount(string $tenant, string $fileId, string $depositDate): array
    {
        $this->foldersData = $this->getEcclesiasticalGroupsFoldersAction->__invoke(false, true);
        $depositList = $this->googleSheetsService->getReceiptsCountsValues($this->client, $fileId);
        $cultDate = $this->googleSheetsService->getCultDate($this->client, $fileId);

        foreach ($this->foldersData as $key => $folderData)
        {
            foreach ($depositList as $deposit)
            {
                $bankReceipts = $this->googleDriveService->listFiles($folderData->folder_id);

                foreach ($bankReceipts as $receipt)
                {
                    $basePathTemp = self::STORAGE_BASE_PATH . 'tenants/' . $tenant . '/temp';
                    $this->googleDriveService->deleteFilesInLocalDirectory($basePathTemp);
                    $downloadedFile = $this->googleDriveService->download($basePathTemp, $receipt);

                    $foundReceipt = $this->OCRExtractDataBankReceiptService->ocrExtractData($downloadedFile['destinationPath'], null, $deposit[0], $depositDate);

                    if($foundReceipt)
                    {
                        $this->arrReceiptsFounded [] = $this->uploadReceipt($downloadedFile['fileUploaded'], self::S3_ENTRIES_RECEIPT_PATH, $tenant);
                        $this->googleDriveService->renameFile($receipt->id, null, 'FILE_READ', null, $cultDate[0][0]);
                        break;
                    }
                }
            }
        }

        return $this->arrReceiptsFounded;
    }



    /**
     * @param string $filePath
     * @param string $destinationDir
     * @param string $tenant
     * @return string
     * @throws GeneralExceptions
     */
    public function uploadReceipt(string $filePath, string $destinationDir, string $tenant): string
    {
        return $this->uploadFile->upload($filePath, $destinationDir, $tenant);
    }



    /**
     *
     * @throws \Exception
     */
    public function setEntryData(array $extractedData, string $entryType, array $depositDate): void
    {
        $currentDate = date('Y-m-d');
        $amount = $this->convertStringAmountToFloat($extractedData[1]);
        $depositDate = date('Y-m-d', strtotime(str_replace('/', '-', $depositDate[0][0])));

        $this->entryData->amount = $amount;
        $this->entryData->comments = 'Entrada registrada automaticamente!';
        $this->entryData->dateEntryRegister = $currentDate;
        $this->entryData->dateTransactionCompensation = $depositDate . self::SUFIX_TIMEZONE;
        $this->entryData->deleted = 0;
        $this->entryData->entryType = $entryType;
        $this->entryData->memberId = null;
        $this->entryData->receipt = null;
        $this->entryData->devolution = 0;
        $this->entryData->residualValue = 0;


        $this->entryData->reviewerId = 18;
        $this->entryData->transactionCompensation = 'compensated';
        $this->entryData->transactionType = 'cash';

        $this->consolidationEntriesData->date = $depositDate;

    }


    /**
     * @param string $amount
     * @return float
     */
    public function convertStringAmountToFloat(string $amount): float
    {
        $stringAmountCleaned = str_replace(['R$', ' ', ','], ['', '', '.'], $amount);
        return (float) $stringAmountCleaned;
    }


    public function setUnidentifiedReceiptData(string $entryType, string $receiptLink, array $data): void
    {
        $this->unidentifiedReceiptData->entryType = $entryType;
        $this->unidentifiedReceiptData->receiptLink = $receiptLink;
        $this->unidentifiedReceiptData->deleted = 0;
        $this->unidentifiedReceiptData->amount = array_key_exists('amount', $data) ? floatval($data['amount']) / 100 : null;
    }
}
