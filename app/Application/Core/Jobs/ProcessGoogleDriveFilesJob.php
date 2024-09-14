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
use Domain\Members\DataTransferObjects\MemberData;
use Google\Service\Drive;
use Google\Service\Exception;
use Google\Service\Drive\DriveFile;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\GoogleDrive\GoogleDriveService;
use Infrastructure\Services\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
use Infrastructure\Util\Storage\S3\UploadFile;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class ProcessGoogleDriveFilesJob
{
    private GoogleDriveService $googleDriveService;
    private CreateEntryAction $createEntryAction;
    private GetMemberByMiddleCPFAction $getMemberByMiddleCPFAction;
    private GetEntryByTimestampValueCpfAction $getEntryByTimestampValueCpfAction;
    private UpdateMiddleCpfMemberAction $updateMiddleCpfMemberAction;
    private UpdateIdentificationPendingEntryAction $updateIdentificationPendingEntryAction;
    private UpdateReceiptLinkEntryAction $updateReceiptLinkEntryAction;
    private UpdateTimestampValueCPFEntryAction $updateTimestampValueCPFEntryAction;
    private GetMemberByCPFAction $getMemberByCPFAction;
    private UploadFile $uploadFile;
    private ConsolidationEntriesData $consolidationEntriesData;
    private EntryData $entryData;
    private  MemberData $memberData;
    private string $entryType;
    protected array $foldersIDs;
    protected Drive $driveInstance;
    protected array $tenantsAllowed = [
        'iebrd',
    ];

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';
    const IDENTIFICATION_PENDING_1 = 1;
    const IDENTIFICATION_PENDING_0 = 0;
    const S3_ENTRIES_RECEIPT_PATH = 'entries/assets/receipts';
    const S3_ENTRIES_RECEIPT_UNIDENTIFIED_PATH = 'entries/assets/receipts/unidentified';


    public function __construct(
        GoogleDriveService $googleDriveService,
        CreateEntryAction $createEntryAction,
        GetMemberByMiddleCPFAction $getMemberByMiddleCPFAction,
        GetMemberByCPFAction $getMemberByCPFAction,
        UpdateMiddleCpfMemberAction $updateMiddleCpfMemberAction,
        GetEntryByTimestampValueCpfAction $getEntryByTimestampValueCpfAction,
        UploadFile $uploadFile,
        UpdateIdentificationPendingEntryAction $updateIdentificationPendingEntryAction,
        UpdateReceiptLinkEntryAction $updateReceiptLinkEntryAction,
        UpdateTimestampValueCPFEntryAction $updateTimestampValueCPFEntryAction,
        EntryData $entryData,
        MemberData $memberData,
        ConsolidationEntriesData $consolidationEntriesData,
    )
    {
        $this->googleDriveService = $googleDriveService;
        $this->createEntryAction = $createEntryAction;
        $this->getMemberByMiddleCPFAction = $getMemberByMiddleCPFAction;
        $this->updateMiddleCpfMemberAction = $updateMiddleCpfMemberAction;
        $this->updateIdentificationPendingEntryAction = $updateIdentificationPendingEntryAction;
        $this->updateReceiptLinkEntryAction = $updateReceiptLinkEntryAction;
        $this->updateTimestampValueCPFEntryAction = $updateTimestampValueCPFEntryAction;
        $this->getEntryByTimestampValueCpfAction = $getEntryByTimestampValueCpfAction;
        $this->getMemberByCPFAction = $getMemberByCPFAction;
        $this->uploadFile = $uploadFile;
        $this->entryData = $entryData;
        $this->memberData = $memberData;
        $this->consolidationEntriesData = $consolidationEntriesData;
    }


    /**
     * @return void
     * @throws Exception
     * @throws TesseractOcrException
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     * @throws TenantCouldNotBeIdentifiedById
     * @throws \Throwable
     */
    public function handle(): void
    {
        foreach ($this->tenantsAllowed as $tenant)
        {
            tenancy()->initialize($tenant);

            $this->driveInstance = $this->googleDriveService->getInstanceGoogleDrive($tenant);
            $this->foldersIDs = config('google.drive.tenants.' . $tenant . '.FOLDERS_IDS');

            foreach ($this->foldersIDs as $key => $folderID)
            {
                $this->entryType = explode('_', $key)[0];
                $files = $this->googleDriveService->listFiles($folderID);

                foreach ($files as $file)
                {
                    $basePathTemp = self::STORAGE_BASE_PATH . 'tenants/' . $tenant . '/temp';
                    $this->googleDriveService->deleteFilesInLocalDirectory($basePathTemp);
                    $downloadedFile = $this->googleDriveService->download($basePathTemp, $file);

                    $extractedData = (new OCRExtractDataBankReceiptService)->ocrExtractData($downloadedFile['destinationPath']);

                    if(count($extractedData) > 0 && $extractedData['status'] == 'SUCCESS')
                    {
                        $middleCpf = $extractedData['data']['middle_cpf'];
                        $member = $this->getMemberByMiddleCPFAction->__invoke($middleCpf);

                        if(is_null($member))
                        {
                            $member = $this->getMemberByCPFAction->__invoke($middleCpf, true);

                            if(!is_null($member))
                            {
                                $this->updateMiddleCpfMemberAction->__invoke($member->id, $middleCpf);
                                $this->setEntryData($extractedData, $member, 'tithe');

                                $entryByTimestampValueCpf = $this->getEntryByTimestampValueCpfAction->__invoke($extractedData['data']['timestamp_value_cpf']);

                                if($entryByTimestampValueCpf != null)
                                {
                                    $this->googleDriveService->deleteFile($file->id);
                                    continue;
                                }
                                else
                                {
                                    $entry = $this->createEntryAction->__invoke($this->entryData, $this->consolidationEntriesData);
                                    $this->updateTimestampValueCPFEntryAction->__invoke($entry->id, $extractedData['data']['timestamp_value_cpf']);
                                }
                            }
                            else
                            {
                                $this->setEntryData($extractedData, $member, 'tithe');
                                $entry = $this->createEntryAction->__invoke($this->entryData, $this->consolidationEntriesData);
                                $this->updateIdentificationPendingEntryAction->__invoke($entry->id, self::IDENTIFICATION_PENDING_1);
                            }
                        }
                        else
                        {
                            $this->setEntryData($extractedData, $member, 'tithe');

                            $entryByTimestampValueCpf = $this->getEntryByTimestampValueCpfAction->__invoke($extractedData['data']['timestamp_value_cpf']);

                            if($entryByTimestampValueCpf != null)
                            {
                                $this->googleDriveService->deleteFile($file->id);
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

                        print_r(response()->json($extractedData));
                    }
                    else if(count($extractedData) > 0 && $extractedData['status'] == 'NOT_IMPLEMENTED')
                    {
                        $this->googleDriveService->renameFile($file->id, null, 'NOT_IMPLEMENTED', $extractedData['data']['institution']);

                        print_r([
                            'status' =>  $extractedData['status'],
                            'data' =>  $extractedData,
                        ]) ;
                    }
                    else if(count($extractedData) > 0 && $extractedData['status'] == 'READING_ERROR')
                    {
                        $fileUploaded = $this->uploadFile->upload($downloadedFile['fileUploaded'], self::S3_ENTRIES_RECEIPT_UNIDENTIFIED_PATH, $tenant);
                        // Incluir registro do comprovante em uma tabela de comprovantes sem identificação
                        $this->googleDriveService->renameFile($file->id, null, 'READING_ERROR', $extractedData['data']['institution']);

                        print_r([
                            'status' =>  $extractedData['status'],
                            'data' =>  $extractedData,
                        ]) ;
                    }
                    else
                    {
                        $this->googleDriveService->renameFile($file->id, null, 'NOT_RECOGNIZED');

                        print_r([
                            'status'    =>  $extractedData['status'],
                            'msg'       =>  'Não foi possível identificar a instituição!',
                        ]) ;
                    }
                }
            }
        }
    }


    /**
     *
     * @throws \Exception
     */
    public function setEntryData(array $extractedData, mixed $member, string $entryType): void
    {
        $currentDate = date('Y-m-d');
        $extractedDate = $extractedData['data']['date'];

        $this->entryData->amount = floatval($extractedData['data']['amount']) / 100;
        $this->entryData->comments = 'Entrada registrada automaticamente!';
        $this->entryData->dateEntryRegister = $currentDate;
        $this->entryData->dateTransactionCompensation = $this->getNextBusinessDay($extractedDate);
        $this->entryData->deleted = 0;
        $this->entryData->devolution = 0;
        $this->entryData->entryType = $entryType;
        $this->entryData->memberId = $member?->id;
        $this->entryData->receipt = null;
        $this->entryData->recipient = null;
        $this->entryData->reviewerId = 18;
        $this->entryData->transactionCompensation = 'compensated';
        $this->entryData->transactionType = 'pix';

        $this->consolidationEntriesData->date = $extractedData['data']['date'];

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
