<?php

namespace Infrastructure\Util\Storage\S3;

use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;

class ConnectS3
{
    public const ERROR_S3 = 'Houve um erro ao processar este objeto, tente mais tarde!';
    private string $env;

    public function getInstance(): S3Client
    {
        $this->env = App::environment();

        return new S3Client([
            'version' => 'latest',
            'region'  => config('s3.environments.' . $this->env . '.S3_DEFAULT_REGION'),
            'endpoint' => config('s3.environments.' . $this->env . '.S3_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => config('s3.environments.' . $this->env . '.S3_ACCESS_KEY_ID'),
                'secret' => config('s3.environments.' . $this->env . '.S3_SECRET_ACCESS_KEY'),
            ],
        ]);
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
