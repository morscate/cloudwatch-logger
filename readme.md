# Cloudwatch logger (inc. embedded metrics)
This is an ALPHA version of the package. Do not use this is production!

This package allows you to log to AWS Cloudwatch. It also allows you to include to log metrics with AWS embedded metric format (https://docs.aws.amazon.com/AmazonCloudWatch/latest/monitoring/CloudWatch_Embedded_Metric_Format_Specification.html).

## Requirements

- PHP >= 8.0
- Laravel >= 9.0

## Installation
To start using the package, you need to install it via Composer:
```
composer require morscate/cloudwatch-logger
```
Set the following environment variables in your .env file:
```
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
CLOUDWATCH_LOG_GROUP_NAME=
```

## Publish config
If you need to publish the config file, you can do so by running the following command:
```
php artisan vendor:publish --tag=cloudwatch-config
```

## Security Vulnerabilities

If you discover a security vulnerability within this project, please email me via [development@morscate.nl](mailto:development@morscate.nl).
