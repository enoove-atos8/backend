<?php

namespace Application\Core\Jobs;

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
use Domain\Ecclesiastical\Groups\Actions\GetReturnReceivingGroupAction;
use Domain\Financial\Receipts\Entries\Unidentified\Actions\CreateUnidentifiedReceiptAction;
use Domain\Financial\Receipts\Entries\Unidentified\DataTransferObjects\UnidentifiedReceiptData;
use Domain\Members\DataTransferObjects\MemberData;
use Google\Service\Drive;
use Google\Service\Exception;
use Google\Service\Drive\DriveFile;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\GoogleDrive\GoogleDriveService;
use Infrastructure\Services\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
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
    private CreateUnidentifiedReceiptAction $createUnidentifiedReceiptAction;
    private GetReturnReceivingGroupAction $getReturnReceivingGroupAction;
    private GetMemberByCPFAction $getMemberByCPFAction;
    private UploadFile $uploadFile;
    private ConsolidationEntriesData $consolidationEntriesData;
    private EntryData $entryData;
    private UnidentifiedReceiptData $unidentifiedReceiptData;
    private  MemberData $memberData;
    private string $entryType;
    private array $allowedTenants = [
        'iebrd'
    ];
    protected Collection $foldersData;

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
        GetMemberByCPFAction $getMemberByCPFAction,
        UpdateMiddleCpfMemberAction $updateMiddleCpfMemberAction,
        GetEntryByTimestampValueCpfAction $getEntryByTimestampValueCpfAction,
        GetEcclesiasticalGroupsFoldersAction $getEcclesiasticalGroupsFoldersAction,
        GetReturnReceivingGroupAction $getReturnReceivingGroupAction,
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

            $this->googleDriveService->defineInstanceGoogleDrive($tenant);
            $this->foldersData = $this->getEcclesiasticalGroupsFoldersAction->__invoke();


            foreach ($this->foldersData as $key => $folderData)
            {
                $this->entryType = $folderData->entry_type;

                $files = $this->googleDriveService->listFiles($folderData->folder_id);

                foreach ($files as $file)
                {
                    $basePathTemp = self::STORAGE_BASE_PATH . 'tenants/' . $tenant . '/temp';
                    $this->googleDriveService->deleteFilesInLocalDirectory($basePathTemp);
                    $downloadedFile = $this->googleDriveService->download($basePathTemp, $file);

                    if(is_array($downloadedFile))
                    {
                        $extractedData = $this->OCRExtractDataBankReceiptService->ocrExtractData($downloadedFile['destinationPath'], $this->entryType);

                        if(count($extractedData) > 0 && $extractedData['status'] == 'SUCCESS')
                        {
                            $middleCpf = $extractedData['data']['middle_cpf'];
                            $cpf = $extractedData['data']['cpf'];
                            $member = null;

                            if($middleCpf != '')
                                $member = $this->getMemberByMiddleCPFAction->__invoke($middleCpf);
                            if ($middleCpf != '' && $member == null)
                            {
                                $member = $this->getMemberByCPFAction->__invoke($middleCpf, true);
                                if(!is_null($member))
                                    $this->updateMiddleCpfMemberAction->__invoke($member->id, $middleCpf);
                            }
                            if ($cpf != '' && $member == null)
                                $member = $this->getMemberByCPFAction->__invoke($cpf);

                            if(is_null($member))
                            {
                                $this->setEntryData($extractedData, $member, $folderData);

                                $entryByTimestampValueCpf = $this->getEntryByTimestampValueCpfAction->__invoke($extractedData['data']['timestamp_value_cpf']);

                                if($entryByTimestampValueCpf != null)
                                {
                                    $this->googleDriveService->renameFile($file->id, null, 'DUPLICATED');
                                    continue;
                                }
                                else
                                {
                                    $entry = $this->createEntryAction->__invoke($this->entryData, $this->consolidationEntriesData);
                                    $this->updateTimestampValueCPFEntryAction->__invoke($entry->id, $extractedData['data']['timestamp_value_cpf']);
                                    $this->updateIdentificationPendingEntryAction->__invoke($entry->id, self::IDENTIFICATION_PENDING_1);
                                }
                            }
                            else
                            {
                                $this->setEntryData($extractedData, $member, $folderData);

                                $entryByTimestampValueCpf = $this->getEntryByTimestampValueCpfAction->__invoke($extractedData['data']['timestamp_value_cpf']);

                                if($entryByTimestampValueCpf != null)
                                {
                                    $this->googleDriveService->renameFile($file->id, null, 'DUPLICATED');
                                    continue;
                                }
                                else
                                {
                                    $entry = $this->createEntryAction->__invoke($this->entryData, $this->consolidationEntriesData);
                                    $this->updateTimestampValueCPFEntryAction->__invoke($entry->id, $extractedData['data']['timestamp_value_cpf']);
                                }
                            }


                            $fileUploaded = $this->uploadFile->upload($downloadedFile['fileUploaded'], self::S3_ENTRIES_RECEIPT_PATH, $tenant);
                            if($fileUploaded != '')
                            {
                                $this->updateReceiptLinkEntryAction->__invoke($entry->id, $fileUploaded);
                                $this->googleDriveService->renameFile($file->id, $fileUploaded, 'FILE_READ', $extractedData['data']['institution']);
                            }

                            //printf(json_encode($extractedData));
                        }
                        else if(count($extractedData) > 0 && $extractedData['status'] == 'NOT_IMPLEMENTED')
                        {
                            $this->googleDriveService->renameFile($file->id, null, 'NOT_IMPLEMENTED', $extractedData['data']['institution']);

                            //printf(json_encode([
                            //    'status' =>  $extractedData['status'],
                            //    'data' =>  $extractedData,
                            //]));
                        }
                        else if(count($extractedData) > 0 && $extractedData['status'] == 'READING_ERROR')
                        {
                            $fileUploaded = $this->uploadFile->upload($downloadedFile['fileUploaded'], self::S3_ENTRIES_RECEIPT_UNIDENTIFIED_PATH, $tenant);

                            $this->setUnidentifiedReceiptData($this->entryType, $fileUploaded, $extractedData['data']);
                            $this->createUnidentifiedReceiptAction->__invoke($this->unidentifiedReceiptData);

                            $this->googleDriveService->renameFile($file->id, null, 'READING_ERROR', $extractedData['data']['institution']);

                            //printf(json_encode([
                            //    'status' =>  $extractedData['status'],
                            //    'data' =>  $extractedData,
                            //]));
                        }
                    }
                }
            }
        }
    }


    /**
     *
     * @throws \Exception
     * @throws Throwable
     */
    public function setEntryData(array $extractedData, mixed $member, $folderData): void
    {
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
        $this->entryData->cultFinancialDataId = null;
        $this->entryData->timestampValueCpf = null;

        if($folderData->entry_type == 'designated')
        {
            $this->entryData->groupReceivedId = $folderData->ecclesiastical_divisions_group_id;

            if($folderData->folder_devolution == 1)
            {
                $this->entryData->devolution = 1;
                $this->entryData->groupReceivedId = $this->getReturnReceivingGroup();
                $this->entryData->groupReturnedId = $folderData->ecclesiastical_divisions_group_id;
            }
        }

        $this->entryData->reviewerId = 18;
        $this->entryData->transactionCompensation = 'compensated';
        $this->entryData->transactionType = 'pix';

        $this->consolidationEntriesData->date = $extractedData['data']['date'];

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


    public function setUnidentifiedReceiptData(string $entryType, string $receiptLink, array $data): void
    {
        $this->unidentifiedReceiptData->entryType = $entryType;
        $this->unidentifiedReceiptData->receiptLink = $receiptLink;
        $this->unidentifiedReceiptData->deleted = 0;
        $this->unidentifiedReceiptData->amount = array_key_exists('amount', $data) ? floatval($data['amount']) / 100 : null;
    }

    /**
     *
     * @throws \Exception
     */
    function getNextBusinessDay($date): string
    {
        $holidays = [
            '01-01', // Confraternização Universal (Ano Novo)
            '02-12', // Carnaval (segunda-feira)
            '02-13', // Carnaval (terça-feira)
            '02-14', // Quarta-feira de Cinzas (ponto facultativo até 14h)
            '03-29', // Sexta-feira Santa
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
}
