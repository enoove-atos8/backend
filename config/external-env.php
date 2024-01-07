<?php

return [
    'godaddy'  =>  [
        'base_url'  =>  'https://api.godaddy.com/v1',
        'key'  =>  'fY15ZyEcodfB_Ru5nBs24fYs1Z1khY2mDbL',
        'secret'  =>  'Lh8hMSKX34noLu7SgrzWuR',
    ],

    'aws'   =>  [
        'development'   =>  [
            'host'  =>  '3.14.147.95',
            's3'    =>  'https://atos8-dev.s3.us-east-2.amazonaws.com/clients/'
        ],
        'production'   =>  [
            'host'  =>  '3.14.147.95',
            's3'    =>  'https://atos8-prod.s3.us-east-2.amazonaws.com/clients/'
        ],
        'local'   =>  [
            'host'  =>  '3.14.147.95',
            's3'    =>  'https://atos8-local.s3.us-east-2.amazonaws.com/clients/'
        ],
    ],

    'app'   =>  [
        'domain'  =>  [
            'local'         =>  'atos8.local',
            'production'    =>  'atos8.com',
            'development'   =>  'atos8.com',
        ]
    ],
];
