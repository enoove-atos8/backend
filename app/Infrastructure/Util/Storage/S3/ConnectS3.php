<?php

namespace Infrastructure\Util\Storage\S3;

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
}
