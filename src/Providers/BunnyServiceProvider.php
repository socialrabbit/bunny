<?php

namespace Bunny\Providers;

use Illuminate\Support\ServiceProvider;
use Bunny\Services\{
    CartService,
    ProductService,
    PaymentService,
    AnalyticsService,
    MarketingService,
    InternationalizationService,
    PortfolioService,
    EcommerceService,
    SecurityService,
    PerformanceService,
    IntegrationService,
    TestingService,
    DeploymentService
};
use Bunny\Console\Commands\InstallCommand;
use Bunny\Console\Commands\CreateWebsiteCommand;
use Bunny\Console\Commands\UninstallCommand;
use Bunny\Providers\WebsiteTypeServiceProvider;

class BunnyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/bunny.php', 'bunny'
        );

        $this->app->register(WebsiteTypeServiceProvider::class);

        // Core Services
        $this->app->singleton(CartService::class);
        $this->app->singleton(ProductService::class);
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(AnalyticsService::class);
        $this->app->singleton(MarketingService::class);
        $this->app->singleton(InternationalizationService::class);

        // New Feature Services
        $this->app->singleton(PortfolioService::class);
        $this->app->singleton(EcommerceService::class);
        $this->app->singleton(SecurityService::class);
        $this->app->singleton(PerformanceService::class);
        $this->app->singleton(IntegrationService::class);
        $this->app->singleton(TestingService::class);
        $this->app->singleton(DeploymentService::class);

        // Register Commands
        $this->commands([
            InstallCommand::class,
            CreateWebsiteCommand::class,
            UninstallCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/bunny.php' => config_path('bunny.php'),
            ], 'bunny-config');

            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/bunny'),
            ], 'bunny-views');

            $this->publishes([
                __DIR__.'/../../resources/assets' => public_path('vendor/bunny'),
            ], 'bunny-assets');

            $this->publishes([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'bunny-migrations');
        }

        // Load Routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');

        // Load Migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Load Views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'bunny');

        // Load Translations
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'bunny');

        // Initialize Services
        $this->initializeServices();
    }

    /**
     * Initialize all services
     */
    protected function initializeServices(): void
    {
        // Initialize Performance Service
        $this->app->make(PerformanceService::class)->initialize();

        // Initialize Security Service
        $this->app->make(SecurityService::class)->initialize();

        // Initialize Integration Service
        $this->app->make(IntegrationService::class)->initialize();

        // Initialize Testing Service
        $this->app->make(TestingService::class)->initialize();

        // Initialize Deployment Service
        $this->app->make(DeploymentService::class)->initialize();
    }
}