<?php

namespace Pmurkin\MongoSchemaDumper;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\SchemaExport;
use App\Console\Commands\SchemaImport;

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
        $this->app['command.schema.export'] = $this->app->share(
            function ($app) {
                return new SchemaExport();
            }
        );

        $this->app['command.schema.import'] = $this->app->share(
            function ($app) {
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
