<?php

namespace Morscate\CloudwatchLogger\Facades;

use Illuminate\Support\Facades\Facade;

class CloudwatchLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'CloudwatchLogger';
    }
}
