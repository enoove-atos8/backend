<?php

namespace Infrastructure\Util\Storage\S3;

use Aws\S3\Exception\S3Exception;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Exceptions\GeneralExceptions;
use Aws\S3\S3Client;

class UploadFile
{
    private ConnectS3 $s3;


    public function __construct(ConnectS3 $connectS3)
    {
        $this->s3 = $connectS3;
    }


    /**
     * @param mixed $file
     * @param string $tenantS3PathObject
     * @param string $tenant
     * @return string
     * @throws GeneralExceptions
     */
    public function upload(mixed $file, string $tenantS3PathObject, string $tenant): string
    {
        $env = App::environment();
        $timestamp = time();
        $formattedTime = date("YmdHis", $timestamp);
        $baseUrl = config('services-hosts.services.s3.environments.' . $env . '.S3_ENDPOINT_EXTERNAL_ACCESS');
        $fileExtension = explode('.', $file->getClientOriginalName())[1];
        $fileName = $formattedTime . '_' . uniqid().'.'.$fileExtension;
        $fullPathFile = $tenantS3PathObject . '/' . $fileName;

        try
        {
            $s3 = $this->s3->getInstance();

            if(!$s3->doesBucketExist($tenant))
            {
                $s3->createBucket(['Bucket' => $tenant,]);
                $this->s3->setBucketAsPublic($tenant, $s3);
            }


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
