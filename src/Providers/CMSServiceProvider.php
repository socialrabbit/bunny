<?php

namespace Kisalay\Bunny\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class CMSServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('bunny.cms', function ($app) {
            return new \Kisalay\Bunny\Services\CMSService($app);
        });
    }

    public function boot()
    {
        $this->loadRoutes();
        $this->loadViews();
        $this->loadConfig();
        $this->loadMigrations();
        $this->loadTranslations();
        $this->registerPolicies();
    }

    protected function loadRoutes()
    {
        Route::middleware('web')
            ->prefix(config('bunny.cms.route_prefix', 'admin'))
            ->group(__DIR__ . '/../Routes/cms.php');
    }

    protected function loadViews()
    {
        View::addNamespace('bunny.cms', __DIR__ . '/../Resources/views/cms');
    }

    protected function loadConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/cms.php' => config_path('bunny/cms.php'),
        ], 'bunny-cms-config');
    }

    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations/cms');
    }

    protected function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang/cms', 'bunny.cms');
    }

    protected function registerPolicies()
    {
        Gate::define('manage-content', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('editor');
        });

        Gate::define('manage-users', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-settings', function ($user) {
            return $user->hasRole('admin');
        });
    }
} 