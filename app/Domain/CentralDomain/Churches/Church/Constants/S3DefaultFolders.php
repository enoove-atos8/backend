<?php

namespace Domain\CentralDomain\Churches\Church\Constants;

class S3DefaultFolders
{
    const S3_DEFAULT_FOLDERS = [
        'financial_transactions'    =>  [
            'entries'   =>  [
                'processed_files',
                'designated'    =>  [
                    'campaigns',
                    'departures',
                    'ebd',
                    'events',
                    'ministries',
                    'organizations',
                    'projects',
                ],
                'tithes',
                'offers',
            ],
            'exits'     =>  []
        ],
    ];
}
