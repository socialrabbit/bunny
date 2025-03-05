<?php
// src/BunnyServiceProvider.php

namespace Bunny;

use Illuminate\Support\ServiceProvider;
use Bunny\Commands\InstallBunny;
use function resource_path;
use function config_path;

class BunnyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish configuration and stub files.
        $this->publishes([
            __DIR__ . '/config/bunny.php' => config_path('bunny.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/stubs' => resource_path('stubs/bunny'),
        ], 'stubs');

        // Register commands if running in console.
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallBunny::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/bunny.php', 'bunny');
    }
}
