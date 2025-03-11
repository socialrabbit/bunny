<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Website Type
    |--------------------------------------------------------------------------
    |
    | This option controls the default website type that will be used when
    | creating a new website without specifying a type.
    |
    */
    'default_type' => 'portfolio',

    /*
    |--------------------------------------------------------------------------
    | Available Website Types
    |--------------------------------------------------------------------------
    |
    | This option controls which website types are available in the package.
    | You can disable types you don't need.
    |
    */
    'types' => [
        'portfolio' => true,
        'ecommerce' => true,
        'educational' => true,
        'healthcare' => true,
        'hospitality' => true,
        'real_estate' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Storage
    |--------------------------------------------------------------------------
    |
    | Configure the storage settings for media files.
    |
    */
    'media' => [
        'disk' => 'public',
        'path' => 'bunny/media',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure the cache settings for the package.
    |
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Settings
    |--------------------------------------------------------------------------
    |
    | Configure the analytics settings.
    |
    */
    'analytics' => [
        'enabled' => true,
        'tracking_id' => env('BUNNY_ANALYTICS_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Settings
    |--------------------------------------------------------------------------
    |
    | Configure the SEO settings.
    |
    */
    'seo' => [
        'meta_description' => 'A professional website built with Bunny',
        'meta_keywords' => 'website, professional, bunny',
        'generate_sitemap' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    |
    | Configure the API settings.
    |
    */
    'api' => [
        'prefix' => 'api/bunny',
        'middleware' => ['api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Settings
    |--------------------------------------------------------------------------
    |
    | Configure the theme settings.
    |
    */
    'theme' => [
        'default' => 'default',
        'custom_css' => true,
        'custom_js' => true,
    ],
]; 