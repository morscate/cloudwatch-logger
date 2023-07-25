<?php

namespace Morscate\CloudwatchLogger;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;
use Aws\Result;
use Illuminate\Support\Str;

class CloudwatchLogger
{
    private CloudwatchClient $client;

    private string $retention = '14';

    private string $streamName;

    private string $namespace;

    private string $message = '';

    private array $metrics = [];

    public function __construct()
    {
        $this->client = $this->client();
    }

    public function stream(string $name): self
    {
        $this->streamName = $name;

        return $this;
    }

    public function namespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function message($message): self
    {
        $this->message = $message;

        return $this;
    }

    public function metric(
        string $name,
        mixed $value,
        string $unit = 'Milliseconds',
    ): self {
        $this->metrics[] = [
            'Name' => $name,
            'Unit' => $unit,
            $name => $value,
        ];

        return $this;
    }

    public function send(): Result
    {
        $entries = [
            $this->formatEntry(),
        ];

        return $this->client->putLogs($this->streamName, $entries);
    }

    private function formatEntry(): array
    {
        $message['message'] = $this->message;

        if (!empty($this->metrics)) {
            $metrics = [];
            foreach ($this->metrics as $metric) {
                $metricName = $metric['Name'];
                $metricValue = $metric[$metricName];

                $metrics = [
                    'Name' => $metricName,
                    'Unit' => $metric['Unit'],
                ];

                $message[$metricName] = $metricValue;
            }

            $message['_aws'] = [
                'Timestamp' => time(),
                'CloudWatchMetrics' => [
                    'Metrics' => $metrics,
                ]
            ];

            if (!empty($this->namespace)) {
                $message['_aws']['CloudWatchMetrics']['Namespace'] = $this->namespace;
            }
        }

        $entry['message'] = json_encode($message);
        $entry['timestamp'] = floor(microtime(true) * 1000);

        return $entry;
    }
}
