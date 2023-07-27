<?php

namespace Morscate\CloudwatchLogger\Facades;

use Aws\Result;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Morscate\CloudwatchLogger\CloudwatchLogger group(string $name): self
 * @method static \Morscate\CloudwatchLogger\CloudwatchLogger stream(string $name): self
 * @method static \Morscate\CloudwatchLogger\CloudwatchLogger namespace(string $namespace): self
 * @method static \Morscate\CloudwatchLogger\CloudwatchLogger message(mixed $message): self
 * @method static \Morscate\CloudwatchLogger\CloudwatchLogger metric(string $name, mixed $value, string $unit = 'Milliseconds'): self
 * @method static \Morscate\CloudwatchLogger\CloudwatchLogger send(): Result
 */
class CloudwatchLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'CloudwatchLogger';
    }
}
