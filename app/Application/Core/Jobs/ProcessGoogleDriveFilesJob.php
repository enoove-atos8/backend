<?php

namespace Application\Core\Jobs;

use Google\Service\Exception;
use Google_Service_Drive_DriveFile;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Infrastructure\Services\GoogleDrive\GoogleDriveService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Infrastructure\Services\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class ProcessGoogleDriveFilesJob extends GoogleDriveService
{

    //use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $driveService;
    protected $foldersIDs;

    protected $driveInstance;
    protected array $tenantsAllowed = [
        'iebrd',
    ];

    public function __construct()
    {
    }


    /**
     * @return void
     * @throws Exception
     * @throws TesseractOcrException
     */
    public function handle(): void
    {
        foreach ($this->tenantsAllowed as $tenant)
        {
            $this->driveInstance = $this->getInstanceGoogleDrive($tenant);
            $this->foldersIDs = config('google.drive.tenants.' . $tenant . '.FOLDERS_IDS');

            foreach ($this->foldersIDs as $folderID)
            {
                $files = $this->listFiles($folderID);

                foreach ($files as $file)
                {
                    $this->deleteLocalDirectory(storage_path('tenants/' . $tenant . '/temp'));
                    $path = $this->download($file, $tenant);

                    $receiptRead = (new OCRExtractDataBankReceiptService)->ocrExtractData($path);

                    if($receiptRead !== false)
                        //Processa e salva a entrada no sistema
                        // Realiza o upload para o MinIO
                        // Renomeia o arquivo no Drive
                        return;
                    else
                        //Pensar em uma lógica para marcar a entrada como não identificado
                        return;

                    //Rename file
                    //$fileMetadata = new Google_Service_Drive_DriveFile();
                    //$fileMetadata->setName('NOVO_NOME');

                    //$file = $this->instance->files->update($file->id, $fileMetadata);
                    //$savePath = storage_path('app/tenants/dev/temp/files/financial/entries/' . $file->name);
                    //$this->driveService->downloadFile($file->id, $savePath);

                    // Realiza OCR
                    // Processa e salva a entrada no sistema
                    // Realiza o upload para o MinIO

                    // Renomeia o arquivo no Drive
                    //$this->driveService->renameFile('arquivo_id', 'novo_nome.txt');
                }
            }
        }
    }


    /**
     * @param $ocrText
     * @return void
     */
    protected function processOCR($ocrText)
    {
        // Processa o texto extraído do OCR e retorna um EntryData
        // Lógica de parsing e construção do EntryData deve ser implementada aqui
    }


    /**
     * @param $filePath
     * @return void
     */
    protected function uploadToMinIO($filePath): void
    {
        /*$s3 = resolve('App\Services\S3Service'); // Supondo que você tenha um S3Service para MinIO
        $tenant = 'nome_do_tenant'; // Defina o tenant conforme sua lógica
        $tenantPath = 'financial/entries';
        $s3->upload($filePath, $tenantPath, $tenant);*/
    }
}
