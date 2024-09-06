<?php

namespace Infrastructure\Services\GoogleDrive;

use Google\Service\Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Str;

class GoogleDriveService
{


    protected Client $client;
    protected Drive $instance;

    const TEMP_FILE_PREFIX_NAME = 'tempfile';


    public function getInstanceGoogleDrive(string $tenant): Drive
    {
        $clientId = config('google.drive.tenants.' . $tenant . '.GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = config('google.drive.tenants.' . $tenant . '.GOOGLE_DRIVE_CLIENT_SECRET');
        $refreshToken = config('google.drive.tenants.' . $tenant . '.GOOGLE_DRIVE_REFRESH_TOKEN');

        $this->client = new Client();

        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->refreshToken($refreshToken);

        $this->client->addScope(Drive::DRIVE);

        $this->instance = new Drive($this->client);

        return $this->instance;
    }


    /**
     * @param string $folderId
     * @return array
     * @throws Exception
     */
    public function listFiles(string $folderId): array
    {
        $response = $this->instance->files->listFiles([
            'q' => "'{$folderId}' in parents and not name contains 'FILE_READ' and not name contains 'NOVO_NOME'",
            'fields' => 'files(id, name)',
        ]);

        return $response->files;
    }


    /**
     * @param $file
     * @param $tenant
     * @return string
     * @throws Exception
     */
    public function download($file, $tenant): string
    {
        $file = $this->instance->files->get($file->id, ['alt' => 'media']);
        $contentType = $file->getHeaderLine('Content-Type');

        if ($contentType == 'image/jpeg')
            $newNameWithExtension = self::TEMP_FILE_PREFIX_NAME . '_' . Str::uuid() . '.jpg';
        if ($contentType == 'application/pdf')
            $newNameWithExtension = self::TEMP_FILE_PREFIX_NAME . '_' . Str::uuid() . '.pdf';

        if (!file_exists(storage_path('tenants/' . $tenant . '/temp')))
            mkdir(storage_path('tenants/' . $tenant . '/temp'), 0777, true);

        $destinationPath = storage_path('tenants/' . $tenant . '/temp/' . $newNameWithExtension);
        file_put_contents($destinationPath, $file->getBody()->getContents());

        return $destinationPath;
    }


    /**
     * @param $fileId
     * @param $newName
     * @return mixed|null
     */
    public function renameFile($fileId, $newName): mixed
    {

    }


    /**
     * Delete an local directory
     */
    protected function deleteLocalDirectory($dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $this->deleteLocalDirectory("$dir/$file");
            }
            //rmdir($dir);
        } else {
            unlink($dir);
        }
    }
}
