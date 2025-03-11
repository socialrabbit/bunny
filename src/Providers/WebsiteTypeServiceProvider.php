<?php

namespace Kisalay\Bunny\Providers;

use Illuminate\Support\ServiceProvider;
use Kisalay\Bunny\WebsiteTypes\PortfolioWebsite;
use Kisalay\Bunny\WebsiteTypes\EcommerceWebsite;
use Kisalay\Bunny\WebsiteTypes\EducationalWebsite;
use Kisalay\Bunny\WebsiteTypes\HealthcareWebsite;
use Kisalay\Bunny\WebsiteTypes\HospitalityWebsite;
use Kisalay\Bunny\WebsiteTypes\RealEstateWebsite;

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
            $this->app->singleton("bunny.website.{$type}", function ($app) use ($class) {
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