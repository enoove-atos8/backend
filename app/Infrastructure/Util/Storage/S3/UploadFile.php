<?php

namespace Infrastructure\Util\Storage\S3;

use Aws\S3\Exception\S3Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;

class UploadFile
{
    private ConnectS3 $s3;


    public function __construct(ConnectS3 $connectS3)
    {
        $this->s3 = $connectS3;
    }


    /**
     * @param mixed $file
     * @param string $relativePath
     * @param string $tenant
     * @param bool $processError
     * @return string
     * @throws GeneralExceptions
     */
    public function upload(mixed $file, string $relativePath, string $tenant, bool $processError = false): string
    {
        if(is_string($file))
            $file = new UploadedFile($file, basename($file), null, null, true);

        $env = App::environment();
        $timestamp = time();
        $formattedTime = date("YmdHis", $timestamp);
        $baseUrl = config('services-hosts.services.s3.environments.' . $env . '.S3_ENDPOINT_EXTERNAL_ACCESS');
        $arrFileParts = explode('.', $file->getClientOriginalName());
        $fileExtension = end($arrFileParts);
        $fileName = $processError ? 'ERROR_' .$formattedTime . '_' . uniqid().'.'.$fileExtension : $formattedTime . '_' . uniqid().'.'.$fileExtension;
        $fullPathFile = $relativePath . '/' . $fileName;

        try
        {
            $s3 = $this->s3->getInstance();

            $s3->putObject([
                'Bucket' => $tenant,
                'Key'    => $fullPathFile,
                'Body' => file_get_contents($file)
            ]);

            return $baseUrl . '/' . $tenant . '/' . $fullPathFile;

        }
        catch (S3Exception $e)
        {
            throw new GeneralExceptions(ConnectS3::UPLOAD_FILE_ERROR_S3, 500);
        }
    }
}
