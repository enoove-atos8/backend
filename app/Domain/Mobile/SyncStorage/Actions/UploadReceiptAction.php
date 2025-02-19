<?php

namespace Domain\Mobile\SyncStorage\Actions;

use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\ConnectS3;

class UploadReceiptAction
{
    private ConnectS3 $s3;


    public function __construct(ConnectS3 $connectS3)
    {
        $this->s3 = $connectS3;
    }

    /**
     * @param mixed $file
     * @param string $path
     * @param string $tenant
     * @return string
     * @throws GeneralExceptions
     */
    public function execute(mixed $file, string $path, string $tenant): string
    {

        $env = App::environment();
        $timestamp = time();
        $formattedTime = date("YmdHis", $timestamp);
        $baseUrl = config('services-hosts.services.s3.environments.' . $env . '.S3_ENDPOINT_EXTERNAL_ACCESS');
        $arrFileParts = explode('.', $file->getClientOriginalName());
        $fileExtension = end($arrFileParts);
        $fileName = $formattedTime . '_' . uniqid().'.'.$fileExtension;
        $fullPathFile = $path . '/' . $fileName;

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
