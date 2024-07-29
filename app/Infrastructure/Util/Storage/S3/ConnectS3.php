<?php

namespace Infrastructure\Util\Storage\S3;

use App\Domain\Churches\Constants\ReturnMessages;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;

class ConnectS3
{
    const GET_INSTANCE_ERROR_S3 = 'Houve um erro ao obter a instância do s3!';
    const UPLOAD_FILE_ERROR_S3 = 'Houve um erro carregar o arquivo!';

    /**
     * @throws GeneralExceptions
     */
    public function getInstance(): S3Client
    {
        $env = App::environment();

        try
        {
            return new S3Client([
                'version' => 'latest',
                'region'  => config('services-hosts.services.s3.environments.' . $env . '.S3_DEFAULT_REGION'),
                'endpoint' => config('services-hosts.services.s3.environments.' . $env . '.S3_ENDPOINT'),
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key'    => config('services-hosts.services.s3.environments.' . $env . '.S3_ACCESS_KEY_ID'),
                    'secret' => config('services-hosts.services.s3.environments.' . $env . '.S3_SECRET_ACCESS_KEY'),
                ],
            ]);
        }
        catch (S3Exception $e)
        {
            throw new GeneralExceptions(self::GET_INSTANCE_ERROR_S3, 500);
        }
    }



    /**
     * @param string $bucketName
     * @param S3Client $instance
     * @return Result
     */
    public function setBucketAsPublic(string $bucketName, S3Client $instance): Result
    {
        return $instance->putBucketPolicy([
            'Bucket' => $bucketName,
            'Policy' => json_encode([
                'Version' => '2012-10-17',
                'Statement' => [
                    [
                        'Action'    =>  [
                            "s3:GetBucketLocation",
                            "s3:ListBucket",
                            "s3:ListBucketMultipartUploads"
                        ],
                        'Effect'    =>  'Allow',
                        'Principal' =>  '*',
                        'Resource'  => [
                           'arn:aws:s3:::' . $bucketName
                        ]
                    ],
                    [
                        'Action'    =>  [
                            "s3:AbortMultipartUpload",
                            "s3:DeleteObject",
                            "s3:GetObject",
                            "s3:ListMultipartUploadParts",
                            "s3:PutObject"
                        ],
                        'Effect'    =>  'Allow',
                        'Principal' =>  '*',
                        'Resource'  => [
                            "arn:aws:s3:::" . $bucketName . "/*"
                        ]
                    ],
                ],
            ]),
        ]);
    }
}
