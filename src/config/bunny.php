<?php
// src/config/bunny.php

return [
    'default_site' => 'portfolio',  // or 'ecommerce'
    'auth_enabled' => false,
    'payment_gateway' => 'stripe',
    'frontend' => [
        'framework' => 'vue', // Options: vue, react, alpine, or none
        'api_type' => 'rest', // Options: rest or graphql
        'ui_library' => 'tailwind', // Options: tailwind, bootstrap, or none
        'components' => true, // Whether to generate frontend components
    ],
    // Define optional packages for extended functionality.
    'optional_packages' => [
        'socialite'  => 'laravel/socialite',
        'sanctum'    => 'laravel/sanctum',
        'permission' => 'spatie/laravel-permission',
        'debugbar'   => 'barryvdh/laravel-debugbar',
        'inertia'    => 'inertiajs/inertia-laravel',
        'livewire'   => 'livewire/livewire',
    ],
];
