<?php
// src/config/bunny.php

return [
    'default_site' => 'portfolio',  // or 'ecommerce'
    'auth_enabled' => false,
    'payment_gateway' => 'stripe',
    // Define optional packages for extended functionality.
    'optional_packages' => [
        'socialite'  => 'laravel/socialite',
        'sanctum'    => 'laravel/sanctum',
        'permission' => 'spatie/laravel-permission',
        'debugbar'   => 'barryvdh/laravel-debugbar'
    ],
];
