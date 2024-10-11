<?php

namespace Infrastructure\Services\GoogleDrive;

use DateTime;
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
        $client = $this->getInstanceGoogleClient($tenant);

        $this->instance = new Drive($client);

        return $this->instance;
    }


    /**
     *
     * @throws \Google\Exception
     */
    public function getInstanceGoogleClient(string $tenant): Client
    {
        $credentialsPath = config('google.drive.tenants.'. $tenant. '.json_path');

        $this->client = new Client();

        $this->client->setAuthConfig($credentialsPath);
        $this->client->addScope(Drive::DRIVE);
        $this->client->addScope(Drive::DRIVE_FILE);
        $this->client->addScope(Sheets::SPREADSHEETS);

        return $this->client;
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
     *
     */
    public function isFile($file): bool
    {
        $fileMetadata = $this->instance->files->get($file->id, ['fields' => 'mimeType']);

        if ($fileMetadata->mimeType == 'application/vnd.google-apps.file')
            return true;
        else
            return false;
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
     * @param string|null $url
     * @param string $readingType
     * @param string|null $institution
     * @param string $dateCult
     * @return void
     * @throws Exception
     */
    public function renameFile(
        $fileId,
        string | null $url = null,
        string $readingType = 'FILE_READ' | 'NOT_IMPLEMENTED' | 'NOT_RECOGNIZED' | 'READING_ERROR' | 'DUPLICATED',
        string | null $institution = 'GENERIC',
        string $dateCult = ''): void
    {
        $newName = '';

        if($readingType == 'FILE_READ')
        {
            if(!is_null($url))
            {
                $parsedUrl = parse_url($url);
                $path = $parsedUrl['path'];
                $newName = $readingType . '_' . $institution . '_' . basename($path);
            }
            else
            {
                $dateCultFormatted = DateTime::createFromFormat('d/m/Y', $dateCult)->format('Ymd');
                $newName = $readingType . '_' . $dateCultFormatted . '_' . Str::uuid();
            }
        }
        elseif ($readingType == 'NOT_IMPLEMENTED' || $readingType == 'NOT_RECOGNIZED' || $readingType == 'READING_ERROR')
        {
            $newName = 'FILE_READ_'. $institution . '_' . $readingType;
        }
        elseif ($readingType == 'DUPLICATED')
        {
            $newName = 'FILE_READ_' . $readingType;
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
