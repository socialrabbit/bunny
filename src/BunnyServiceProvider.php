<?php
// src/BunnyServiceProvider.php

namespace Bunny;

use Illuminate\Support\ServiceProvider;
use Bunny\Commands\InstallBunny;
use Bunny\Commands\GenerateFrontend;
use Bunny\Commands\GenerateBackend;
use Bunny\Commands\GenerateAPI;
use function resource_path;
use function config_path;

class BunnyServiceProvider extends ServiceProvider
{
    /**
     * The package's base directory.
     *
     * @var string
     */
    protected $packagePath;

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->packagePath = __DIR__;
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load cached configuration if available
        if (file_exists($cachedConfigPath = $this->packagePath . '/config/cached.php')) {
            $this->mergeConfigFrom($cachedConfigPath, 'bunny');
        }

        // Publish configuration and stub files.
        $this->publishes([
            $this->packagePath . '/config/bunny.php' => config_path('bunny.php'),
        ], 'config');

        $this->publishes([
            $this->packagePath . '/stubs' => resource_path('stubs/bunny'),
        ], 'stubs');

        // Register commands if running in console.
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallBunny::class,
                GenerateFrontend::class,
                GenerateBackend::class,
                GenerateAPI::class,
            ]);
        }

        // Register package routes
        $this->loadRoutesFrom($this->packagePath . '/routes/web.php');
        $this->loadRoutesFrom($this->packagePath . '/routes/api.php');

        // Register package views
        $this->loadViewsFrom($this->packagePath . '/resources/views', 'bunny');

        // Register package translations
        $this->loadTranslationsFrom($this->packagePath . '/resources/lang', 'bunny');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom($this->packagePath . '/config/bunny.php', 'bunny');

        // Register package bindings
        $this->app->singleton('bunny', function ($app) {
            return new Bunny($app);
        });

        // Register package facades
        $this->app->bind('bunny.frontend', function ($app) {
            return new FrontendManager($app);
        });

        $this->app->bind('bunny.backend', function ($app) {
            return new BackendManager($app);
        });

        $this->app->bind('bunny.api', function ($app) {
            return new APIManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'bunny',
            'bunny.frontend',
            'bunny.backend',
            'bunny.api',
        ];
    }
}
