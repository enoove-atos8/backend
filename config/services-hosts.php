<?php

return [
    'cloudflare'    =>  [
        'credentials'  =>  [
            'email'  =>  'rafaelhenrique10101@gmail.com',
            'key'  =>  '5ea55f71808f204f0a0fc4a3863c6b379f6ff',
            'zone_id'  =>  'e8bce86dd0fb4d4b1b7d037b65e4accd',
        ],
        'url'  =>  [
            'base'  =>  'https://api.cloudflare.com/client/v4/zones/',
        ],
    ],
    'environments'   =>  [
        'stage'   =>  [
            'host'      =>  '45.164.244.54',
            'domain'    =>  'atos8.com',
        ],
        'production'   =>  [
            'host'  =>  '45.164.244.54',
            'domain'    =>  'atos8.com',
        ],
        'local'   =>  [
            'host'  =>  '',
            'domain'    =>  'atos8.local',
        ],
    ],
    'services'  =>  [
        's3'    =>  [
            'environments'   =>  [
                'stage'   =>  [
                    'S3_BASE_URL'                   =>  'https://s3.atos8.com/api/v1/buckets/',
                    'S3_ACCESS_KEY_ID'              =>  'rhms',
                    'S3_SECRET_ACCESS_KEY'          =>  'Souza.9559',
                    'S3_DEFAULT_REGION'             =>  'us-east-1',
                    'S3_ENDPOINT'                   =>  'https://s3-api.atos8.com',
                    'S3_ENDPOINT_EXTERNAL_ACCESS'   =>  'https://s3-api.atos8.com',
                    'S3_USE_PATH_STYLE_ENDPOINT'    =>  true,
                ],
                'production'   =>  [
                    'S3_BASE_URL'                   =>  'https://s3.atos8.com/api/v1/buckets/',
                    'S3_ACCESS_KEY_ID'              =>  'rhms',
                    'S3_SECRET_ACCESS_KEY'          =>  'Souza.9559',
                    'S3_DEFAULT_REGION'             =>  'us-east-1',
                    'S3_ENDPOINT'                   =>  'https://s3-api.atos8.com',
                    'S3_ENDPOINT_EXTERNAL_ACCESS'   =>  'https://s3-api.atos8.com',
                    'S3_USE_PATH_STYLE_ENDPOINT'    =>  true,
                ],
                'local'   =>  [
                    'S3_BASE_URL'                   =>  'http://s3.atos8.local:9001/api/v1/buckets/',
                    'S3_ACCESS_KEY_ID'              =>  'rhms',
                    'S3_SECRET_ACCESS_KEY'          =>  'Souza.9559',
                    'S3_DEFAULT_REGION'             =>  'us-east-1',
                    'S3_ENDPOINT'                   =>  'http://minio:9000',
                    'S3_ENDPOINT_EXTERNAL_ACCESS'   =>  'http://s3.atos8.local:9090',
                    'S3_USE_PATH_STYLE_ENDPOINT'    =>  true,
                ],
            ],
        ]
    ]
];
