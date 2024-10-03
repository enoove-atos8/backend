<?php

namespace Infrastructure\Services\GoogleDrive;

use Google\Service;
use Google\Service\Drive\DriveFile;
use Google\Service\Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoogleDriveService
{


    protected Client $client;
    protected Drive $instance;
    private DriveFile $driveFile;
    private Drive $driveService;

    const TEMP_FILE_PREFIX_NAME = 'tempfile-job';



    public function __construct(DriveFile $driveFile, Drive $driveService)
    {
        $this->driveFile = $driveFile;
        $this->driveService = $driveService;
    }


    /**
     * @throws \Exception
     */
    public function defineInstanceGoogleDrive(string $tenant): Drive
    {
        $credentialsPath = config('google.drive.tenants.'. $tenant. '.json_path');

        $this->client = new \Google_Client();

        $this->client->setAuthConfig($credentialsPath);

        $this->client->addScope(\Google_Service_Drive::DRIVE);
        $this->client->addScope(\Google_Service_Sheets::SPREADSHEETS);

        // Inicializando o serviço do Google Drive
        $this->instance = new \Google_Service_Drive($this->client);

        return $this->instance;

        /*$this->client = new Client();

        $this->client->setAuthConfig($credentialsPath);
        $this->client->addScope(Drive::DRIVE);
        $this->client->addScope(Sheets::SPREADSHEETS);

        $this->instance = new Drive($this->client);

        return $this->instance;*/
    }


    /**
     * @param string $folderId
     * @return array
     * @throws Exception
     */
    public function listFiles(string $folderId): array
    {
        $response = $this->instance->files->listFiles([
            'q' => "'{$folderId}' in parents and not name contains 'FILE_READ'",
            'fields' => 'files(id, name, parents)',
        ]);

        return $response->files;
    }


    /**
     * @param $basePathTemp
     * @param $file
     * @return array|bool
     * @throws Exception
     */
    public function download($basePathTemp, $file): array | bool
    {
        $fileMetadata = $this->instance->files->get($file->id, ['fields' => 'mimeType']);

        if ($fileMetadata->mimeType !== 'application/vnd.google-apps.folder')
        {
            $file = $this->instance->files->get($file->id, ['alt' => 'media']);
            $physicalFile = $file->getBody()->getContents();

            $contentType = $file->getHeaderLine('Content-Type');

            if ($contentType == 'image/jpeg')
                $newNameWithExtension = self::TEMP_FILE_PREFIX_NAME . '_' . Str::uuid() . '.jpg';
            if ($contentType == 'application/pdf')
                $newNameWithExtension = self::TEMP_FILE_PREFIX_NAME . '_' . Str::uuid() . '.pdf';

            if (!file_exists($basePathTemp))
                mkdir($basePathTemp, 0777, true);

            $destinationPath = $basePathTemp . '/' . $newNameWithExtension;
            file_put_contents($destinationPath, $physicalFile);

            $uploadedFile = new UploadedFile(
                $destinationPath,
                $newNameWithExtension,
                $contentType,
                null,
                true
            );

            return [
                'destinationPath'   =>  $destinationPath,
                'fileUploaded'      => $uploadedFile
            ];
        }
        else
        {
            return false;
        }
    }



    /**
     * @param $fileId
     * @param null $url
     * @param string $readingType
     * @param string $institution
     * @return void
     * @throws Exception
     */
    public function renameFile(
        $fileId,
        $url = null,
        string $readingType = 'FILE_READ' | 'NOT_IMPLEMENTED' | 'NOT_RECOGNIZED' | 'READING_ERROR',
        string $institution = 'GENERIC'): void
    {
        $newName = '';

        if($readingType == 'FILE_READ')
        {
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'];
            $newName = $readingType . '_' . $institution . '_' . basename($path);
        }
        elseif ($readingType == 'NOT_IMPLEMENTED' || 'NOT_RECOGNIZED' || 'READING_ERROR')
        {
            $newName = 'FILE_READ_'. $institution . '_' . $readingType;
        }

        $this->driveFile->setName($newName);
        $this->instance->files->update($fileId, $this->driveFile);
    }


    /**
     * @param $fileId
     * @return void
     * @throws Exception
     */
    public function deleteFile($fileId): void
    {
        $this->instance->files->delete($fileId);
    }



    /**
     * Delete a local directory
     */
    public function deleteFilesInLocalDirectory($dir): void
    {
        if (!file_exists($dir))
            return;

        if (is_dir($dir))
        {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file)
            {
                $filePath = "$dir/$file";

                if (is_dir($filePath))
                    $this->deleteFilesInLocalDirectory($filePath);
                elseif (is_file($filePath) && str_starts_with($file, self::TEMP_FILE_PREFIX_NAME))
                    unlink($filePath);
            }
        }
    }
}
