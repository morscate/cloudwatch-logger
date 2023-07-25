<?php

namespace Morscate\CloudwatchLogger;

use Illuminate\Support\ServiceProvider;

class CloudwatchLoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->configure();

        $this->offerPublishing();

        $this->app->bind('CloudwatchLogger', function() {
            return new CloudwatchLogger();
        });
    }

    /**
     * Setup the configuration.
     */
    protected function configure(): void
    {
        $source = realpath($raw = __DIR__.'/../config/cloudwatch.php') ?: $raw;

        $this->mergeConfigFrom($source, 'cloudwatch');
    }

    /**
     * Setup the resource publishing groups.
     */
    protected function offerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/cloudwatch.php' => config_path('cloudwatch.php'),
        ], 'cloudwatch-config');
    }
}
