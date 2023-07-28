<?php

namespace Morscate\CloudwatchLogger;

use Aws\Result;

class CloudwatchLogger
{
    private string $retention = '14';

    private ?string $groupName = null;

    private string $streamName;

    private string $namespace;

    private mixed $message = '';

    private array $metrics = [];

    private array $dimensions = [];

    public function __construct()
    {
        $this->dimensions['Environment'] = config('cloudwatch.environment', 'local');
    }

    public function group(string $name): self
    {
        $this->groupName = $name;

        return $this;
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

    public function message(mixed $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function addMetric(
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

    public function addDimension(
        string $name,
        mixed $value,
    ): self {
        $this->dimensions[$name] = $value;

        return $this;
    }

    private function formatDimensionSets(): array
    {
        return array_keys($this->dimensions);
    }

    private function formatEntry(): array
    {
        $message['message'] = $this->message;

        $message['_aws'] = [
            'Timestamp' => time(),
            'CloudWatchMetrics' => [],
        ];

        if (! empty($this->namespace)) {
            $message['_aws']['CloudWatchMetrics']['Namespace'] = $this->namespace;
        }

        if (! empty($this->dimensions)) {
            $message['_aws']['CloudWatchMetrics']['Dimensions'] = $this->formatDimensionSets();

            $message = array_merge($message, $this->dimensions);
        }

        if (! empty($this->metrics)) {
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

            $message['_aws']['CloudWatchMetrics']['Metrics'] = $metrics;
        }

        $entry['message'] = json_encode($message);
        $entry['timestamp'] = floor(microtime(true) * 1000);

        return $entry;
    }

    public function send(): Result
    {
        $entries = [
            $this->formatEntry(),
        ];

        $client = new CloudwatchClient($this->groupName);

        return $client->putLogs($this->streamName, $entries);
    }
}
