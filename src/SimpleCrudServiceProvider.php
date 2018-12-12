<?php

namespace Rajmasha\SimpleCrud;

use Illuminate\Support\ServiceProvider;

class SimpleCrudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../publish/views/' => base_path('resources/views/'),
        ]);

        $this->publishes([
            __DIR__ . '/stubs/' => base_path('resources/simple-crud/stubs'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            Commands\CrudGeneratorCommand::class,
            Commands\ControllerGeneratorCommand::class,
            Commands\ModelGeneratorCommand::class,
            Commands\MigrationGeneratorCommand::class,
            Commands\ViewGeneratorCommand::class,
        ]);

    }
}
