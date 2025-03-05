# Bunny - Laravel Scaffolding Made Easy

Bunny is a Laravel package that provides easy scaffolding for common website types like portfolios and e-commerce sites. It helps you quickly set up a new website with all the necessary boilerplate code and best practices.

## Installation

You can install the package via composer:

```bash
composer require socialrabbit/bunny
```

After installing the package, publish the configuration file:

```bash
php artisan vendor:publish --provider="Bunny\BunnyServiceProvider"
```

## Usage

To scaffold a new website, use the `bunny:install` command:

```bash
php artisan bunny:install
```

The command will guide you through the following steps:
1. Choose the type of website (portfolio or e-commerce)
2. Enter the main model name
3. Define model fields
4. Choose optional features:
   - Authentication scaffolding
   - Payment gateway integration (for e-commerce)
   - Additional packages
   - CMS functionality

## Features

- Quick scaffolding for different website types
- Model, migration, and controller generation
- Optional authentication scaffolding
- Optional payment gateway integration
- Built-in CMS functionality
- Customizable templates and stubs

## Configuration

You can customize the package behavior in the `config/bunny.php` file:

```php
return [
    'default_site' => 'portfolio',  // or 'ecommerce'
    'auth_enabled' => false,
    'payment_gateway' => 'stripe',
    'optional_packages' => [
        'socialite'  => 'laravel/socialite',
        'sanctum'    => 'laravel/sanctum',
        'permission' => 'spatie/laravel-permission',
        'debugbar'   => 'barryvdh/laravel-debugbar'
    ],
];
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email kisalnelaka6@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information. 
