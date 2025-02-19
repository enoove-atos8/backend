<?php

namespace Domain\CentralDomain\Churches\Church\Constants;

class S3DefaultFolders
{
    const S3_DEFAULT_FOLDERS = [
        'sync_storage'    =>  [
            'financial'   =>  [
                'shared_receipts'   => [
                    'entries'   =>  [],
                    'exits'   =>  [],
                ],
                'stored_receipts'   =>  [
                    'entries'   =>  [],
                    'exits'   =>  []
                ],
            ],
        ],
    ];
}
