<?php

namespace Infrastructure\Util\Storage\S3;

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
