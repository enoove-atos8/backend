<?php

namespace Infrastructure\Services\External\minIO;

use Aws\S3\Exception\S3Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\ConnectS3;

class MinioStorageService
{
    private ConnectS3 $s3;

    public function __construct(ConnectS3 $connectS3)
    {
        $this->s3 = $connectS3;
    }


    /**
     * @param $file
     * @param string $relativePath
     * @param string $tenant
     * @param bool $processError
     * @return string
     * @throws GeneralExceptions
     */
    public function upload($file, string $relativePath, string $tenant, bool $processError = false): string
    {
        if (is_string($file)) {
            $file = new UploadedFile($file, basename($file), null, null, true);
        }

        $env = App::environment();
        $timestamp = time();
        $formattedTime = date("YmdHis", $timestamp);
        $baseUrl = config('services-hosts.services.s3.environments.' . $env . '.S3_ENDPOINT_EXTERNAL_ACCESS');
        $fileExtension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $fileName = $processError ? 'ERROR_' .$formattedTime . '_' . uniqid().'.'.$fileExtension : $formattedTime . '_' . uniqid().'.'.$fileExtension;
        $fullPathFile = $relativePath . '/' . $fileName;
        $contentType = $file->getMimeType();

        try {
            $s3 = $this->s3->getInstance();
            $s3->putObject([
                'Bucket'        => $tenant,
                'Key'           => $fullPathFile,
                'Body'          => file_get_contents($file),
                'ACL'           => 'public-read',
                'ContentType'   => $contentType
            ]);

            return $baseUrl . '/' . $tenant . '/' . $fullPathFile;

        } catch (S3Exception $e) {
            throw new GeneralExceptions(ConnectS3::UPLOAD_FILE_ERROR_S3, 500, $e);
        }
    }




    /**
     * @param string $filePath
     * @param string $tenant
     * @return bool
     * @throws GeneralExceptions
     */
    public function delete(string $filePath, string $tenant): bool
    {
        try {
            $s3 = $this->s3->getInstance();
            $s3->deleteObject([
                'Bucket' => $tenant,
                'Key'    => $filePath
            ]);
            return true;
        } catch (S3Exception $e) {
            throw new GeneralExceptions("Error deleting file from MinIO", 500);
        }
    }




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
                elseif (is_file($filePath))
                    unlink($filePath);
            }
        }
    }


    /**
     * @param string $tenant
     * @param string $path
     * @return array
     * @throws GeneralExceptions
     */
    public function getFilesByPath(string $path, string $tenant): array
    {
        try {
            $s3 = $this->s3->getInstance();
            $objects = $s3->listObjectsV2([
                'Bucket' => $tenant,
                'Prefix' => $path
            ]);

            return isset($objects['Contents']) ? array_map(fn($obj) => $obj['Key'], $objects['Contents']) : [];
        } catch (S3Exception $e) {
            throw new GeneralExceptions("Error retrieving Files by path from MinIO", 500);
        }
    }



    /**
     * @param string $filePath
     * @param string $tenant
     * @return string
     */
    public function getFileUrl(string $filePath, string $tenant): string
    {
        $env = App::environment();
        $baseUrl = config('services-hosts.services.s3.environments.' . $env . '.S3_ENDPOINT_EXTERNAL_ACCESS');
        return $baseUrl . '/' . $tenant . '/' . $filePath;
    }



    /**
     * @param string $sourcePath
     * @param string $destinationPath
     * @param string $tenant
     * @return bool
     * @throws GeneralExceptions
     */
    public function moveFile(string $sourcePath, string $destinationPath, string $tenant): bool
    {
        try {
            $s3 = $this->s3->getInstance();

            $s3->copyObject([
                'Bucket'     => $tenant,
                'CopySource' => "{$tenant}/{$sourcePath}",
                'Key'        => $destinationPath
            ]);

            $this->delete($sourcePath, $tenant);
            return true;
        } catch (S3Exception $e) {
            throw new GeneralExceptions("Error moving file in MinIO", 500);
        }
    }


    /**
     * @param string $currentPath
     * @param string $newName
     * @param string $tenant
     * @return bool
     * @throws GeneralExceptions
     */
    public function renameFile(string $currentPath, string $newName, string $tenant): bool
    {
        $pathParts = pathinfo($currentPath);
        $newPath = $pathParts['dirname'] . '/' . $newName;
        return $this->moveFile($currentPath, $newPath, $tenant);
    }


    /**
     * @param string $filePath
     * @param string $tenant
     * @param string $localPath
     * @return array|bool
     * @throws GeneralExceptions
     */
    public function downloadFile(string $filePath, string $tenant, string $localPath): array | bool
    {
        try
        {
            $s3 = $this->s3->getInstance();
            $result = $s3->getObject([
                'Bucket' => $tenant,
                'Key'    => $filePath
            ]);

            $contentType = $result['ContentType'];
            $fullFileNamePdf = null;
            $newNameWithExtension = 'temp_' . Str::uuid();

            if ($contentType == 'image/jpeg') {
                $newNameWithExtension .= '.jpg';
            } elseif ($contentType == 'application/pdf') {
                $newNameWithExtension .= '.pdf';
                $fullFileNamePdf = $localPath . '/' . $newNameWithExtension;
            } else {
                $newNameWithExtension .= '.' . pathinfo($filePath, PATHINFO_EXTENSION);
            }

            if (!file_exists($localPath)) {
                mkdir($localPath, 0777, true);
            }

            $destinationPath = $localPath . '/' . $newNameWithExtension;
            file_put_contents($destinationPath, $result['Body']);

            if ($contentType == 'application/pdf') {
                $newNameWithExtension = $this->convertPdfToJpg($destinationPath);
                $destinationPath = $newNameWithExtension;
            }

            $uploadedFile = new UploadedFile(
                $destinationPath,
                basename($destinationPath),
                $contentType,
                null,
                true
            );

            return [
                'destinationPath'   => $destinationPath,
                'fullFileNamePdf'   => $fullFileNamePdf,
                'fileUploaded'      => $uploadedFile
            ];
        }
        catch (S3Exception $e)
        {
            return false;
        }
    }



    /**
     * Convert pdf to image
     */
    private function convertPdfToJpg(string $file): string
    {
        $resolutionImage = 250;
        $fileNameToJpg = '';

        if(strpos($file, '.pdf'))
        {
            $fileNameToJpg = preg_replace('/\.pdf$/', '', $file);
            exec("pdftoppm -jpeg -f 1 -l 1 -rx $resolutionImage -ry $resolutionImage $file $fileNameToJpg");

            $fileNameWithNumberSufix = preg_replace('/\.jpg$/', '-1.jpg', $fileNameToJpg . '.jpg');

            rename($fileNameWithNumberSufix, $fileNameToJpg . '.jpg');
        }

        return $fileNameToJpg . '.jpg';
    }
}
