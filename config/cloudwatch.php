<?php

return [
    'aws_access_key' => env('AWS_ACCESS_KEY_ID'),

    'aws_access_secret' => env('AWS_SECRET_ACCESS_KEY'),

    'aws_region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    
    'environment' => env('CLOUDWATCH_ENVIRONMENT', 'local'),

    'log_group_name' => env('CLOUDWATCH_LOG_GROUP_NAME', ''),

    'batch_size' => env('CLOUDWATCH_BATCH_SIZE', 10000),

    'retention_days' => env('CLOUDWATCH_RETENTION_DAYS', 10000),
];
