<?php

namespace Bunny\Providers;

use Illuminate\Support\ServiceProvider;
use Bunny\Services\CartService;
use Bunny\Services\ProductService;
use Bunny\Services\PaymentService;
use Bunny\Services\AnalyticsService;
use Bunny\Services\MarketingService;
use Bunny\Services\InternationalizationService;

class EcommerceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('bunny.cart', function ($app) {
            return new CartService();
        });

        $this->app->singleton('bunny.products', function ($app) {
            return new ProductService();
        });

        $this->app->singleton('bunny.payments', function ($app) {
            return new PaymentService();
        });

        $this->app->singleton('bunny.analytics', function ($app) {
            return new AnalyticsService();
        });

        $this->app->singleton('bunny.marketing', function ($app) {
            return new MarketingService();
        });

        $this->app->singleton('bunny.i18n', function ($app) {
            return new InternationalizationService();
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/ecommerce.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/ecommerce');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/ecommerce', 'bunny-ecommerce');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/ecommerce', 'bunny-ecommerce');
    }

    public function provides()
    {
        return [
            'bunny.cart',
            'bunny.products',
            'bunny.payments',
            'bunny.analytics',
            'bunny.marketing',
            'bunny.i18n',
        ];
    }
} 