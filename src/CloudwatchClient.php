<?php

namespace Morscate\CloudwatchLogger;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;
use Aws\Result;

class CloudwatchClient
{
    private CloudWatchLogsClient $client;

    private string $retention = '14';

    private string $groupName;

    public function __construct(string $groupName = null)
    {
        $this->groupName = $groupName ?? config('cloudwatch.log_group_name');

        $this->client = $this->client();
    }

    public function getStreamLogs(string $streamName): Result
    {
        return $this->client
            ->getLogEvents([
                'logGroupName' => $this->groupName,
                'logStreamName' => $streamName,
            ]);
    }

    public function putLogs(string $streamName, array $entries): Result
    {
        $data = [
            'logGroupName' => $this->groupName,
            'logStreamName' => $streamName,
            'logEvents' => $entries,
        ];

        try {
            $response = $this->client->putLogEvents($data);
        } catch (CloudWatchLogsException $exception) {
            if ($exception->getAwsErrorCode() === 'ResourceNotFoundException') {
                $this->createLogStream($streamName);
                $response = $this->putLogs($streamName, $entries);
            }

            Log::error($exception->getMessage(), $exception->toArray());
        }

        return $response;
    }

    public function createLogStream(string $streamName): void
    {
        // fetch existing streams
        $existingStreams = $this->client
            ->describeLogStreams([
                'logGroupName' => $this->groupName,
                'logStreamNamePrefix' => $streamName,
            ])
            ->get('logStreams');

        // extract existing streams names
        $existingStreamsNames = array_map(
            function ($stream) use ($streamName) {
                // set sequence token
                if ($stream['logStreamName'] === $streamName && isset($stream['uploadSequenceToken'])) {
                    $this->sequenceToken = $stream['uploadSequenceToken'];
                }

                return $stream['logStreamName'];
            },
            $existingStreams
        );

        // create stream if not created
        if (! in_array($streamName, $existingStreamsNames, true)) {
            try {
                $this->client->createLogStream([
                        'logGroupName' => $this->groupName,
                        'logStreamName' => $streamName,
                    ]);
            } catch (CloudWatchLogsException $exception) {
                // mean that the log stream already exists, so we can just proceed
            }
        }
    }

    private function client(): CloudWatchLogsClient
    {
        return new CloudWatchLogsClient([
            'region' => config('cloudwatch.aws_region', 'us-east-1'),
            'version' => config('cloudwatch.version', 'latest'),
            'credentials' => [
                'key' => config('cloudwatch.aws_access_key'),
                'secret' => config('cloudwatch.aws_access_secret'),
            ],
        ]);
    }
}
