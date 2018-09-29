<?php

namespace Pmurkin\MongoSchemaDumper;

use Illuminate\Support\ServiceProvider;
use Pmurkin\MongoSchemaDumper\Console\SchemaExport;
use Pmurkin\MongoSchemaDumper\Console\SchemaImport;

class MongoSchemaDumperServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'command.schema.export',
            function() {
                return new SchemaExport();
            }
        );

        $this->app->singleton(
            'command.schema.import',
            function() {
                return new SchemaImport();
            }
        );

        $this->commands('command.schema.export', 'command.schema.import');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('command.schema.export', 'command.schema.import');
    }
}
