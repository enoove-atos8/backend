<?php

return [
    'environments'   =>  [
        'stage'   =>  [
            'S3_BASE_URL'                   =>  'https://s3.stage.atos8.com/api/v1/buckets/',
            'S3_ACCESS_KEY_ID'              =>  'rhms',
            'S3_SECRET_ACCESS_KEY'          =>  'Souza.9559',
            'S3_DEFAULT_REGION'             =>  'us-east-1',
            'S3_ENDPOINT'                   =>  'https://s3.br.api.stage.atos8.com',
            'S3_ENDPOINT_EXTERNAL_ACCESS'   =>  'https://s3.br.api.stage.atos8.com',
            'S3_USE_PATH_STYLE_ENDPOINT'    =>  true,
        ],
        'production'   =>  [

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
];
