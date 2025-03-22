<?php

namespace Bunny\Providers;

use Illuminate\Support\ServiceProvider;
use Bunny\WebsiteTypes\PortfolioWebsite;
use Bunny\WebsiteTypes\EcommerceWebsite;
use Bunny\WebsiteTypes\EducationalWebsite;
use Bunny\WebsiteTypes\HealthcareWebsite;
use Bunny\WebsiteTypes\HospitalityWebsite;
use Bunny\WebsiteTypes\RealEstateWebsite;

class WebsiteTypeServiceProvider extends ServiceProvider
{
    protected $websiteTypes = [
        'portfolio' => PortfolioWebsite::class,
        'ecommerce' => EcommerceWebsite::class,
        'educational' => EducationalWebsite::class,
        'healthcare' => HealthcareWebsite::class,
        'hospitality' => HospitalityWebsite::class,
        'real_estate' => RealEstateWebsite::class,
    ];

    public function register()
    {
        foreach ($this->websiteTypes as $type => $class) {
            $this->app->singleton("bunny.website.{$type}", function ($app) use ($class, $type) {
                return new $class($type);
            });
        }
    }

    public function boot()
    {
        //
    }

    public function getWebsiteTypes()
    {
        return $this->websiteTypes;
    }
}