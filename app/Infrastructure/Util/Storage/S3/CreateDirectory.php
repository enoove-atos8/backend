<?php

namespace Infrastructure\Util\Storage\S3;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Domain\Financial\SyncStorage\Actions\AddPathSyncStorageAction;
use Domain\Financial\SyncStorage\DataTransferObjects\SyncStorageData;
use Illuminate\Support\Facades\App;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateDirectory
{
    private ConnectS3 $s3;

    const TREE_FINANCIAL_ENTRIES_DESIGNATED = ['financial', 'entries', 'designated'];

    public function __construct
    (
        ConnectS3 $connectS3,
    )
    {
        $this->s3 = $connectS3;
    }


    /**
     * @throws GeneralExceptions
     */
    public function createDirectory(string $fullPath, $tenant): void
    {
        $this->s3->getInstance()->putObject([
            'Bucket' => $tenant,
            'Key'    => $fullPath . '/empty',
            'Body'   => '',
        ]);
    }
}
