<?php

namespace Domain\CentralDomain\Churches\Church\Constants;

class S3DefaultFolders
{
    const S3_DEFAULT_FOLDERS = [
        'sync_storage'    =>  [
            'financial'   =>  [
                'entries'   =>  [
                    'shared_receipts'   =>  [
                        'designated'    =>  [
                            'campaigns',
                            'departures',
                            'ebd',
                            'events',
                            'ministries',
                            'organizations',
                            'projects',
                        ],
                        'offers',
                        'tithes',
                    ],
                    'stored_receipts'   =>  [
                        'designated',
                        'offers',
                        'tithes',
                    ]
                ],
                'exits'   =>  [
                    'shared_receipts'   =>  [],
                    'stored_receipts'   =>  []
                ],
            ],
        ],
    ];
}
