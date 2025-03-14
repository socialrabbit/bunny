# 🐰 Bunny - Laravel Website Scaffolding Package

<div align="center">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/socialrabbit/bunny.svg?style=flat-square)](https://packagist.org/packages/socialrabbit/bunny)
[![Total Downloads](https://img.shields.io/packagist/dt/socialrabbit/bunny.svg?style=flat-square)](https://packagist.org/packages/socialrabbit/bunny)
[![License](https://img.shields.io/packagist/l/socialrabbit/bunny.svg?style=flat-square)](https://packagist.org/packages/socialrabbit/bunny)
[![PHP Version](https://img.shields.io/packagist/php-v/socialrabbit/bunny.svg?style=flat-square)](https://packagist.org/packages/socialrabbit/bunny)
[![Laravel Version](https://img.shields.io/packagist/dependency-v/socialrabbit/bunny/illuminate/support.svg?style=flat-square)](https://packagist.org/packages/socialrabbit/bunny)
[![Build Status](https://img.shields.io/github/workflow/status/socialrabbit/bunny/tests?label=tests&branch=main&style=flat-square)](https://github.com/socialrabbit/bunny/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/socialrabbit/bunny.svg?style=flat-square)](https://codecov.io/gh/socialrabbit/bunny)

> 🚀 The Ultimate Laravel Website Builder & Scaffolding Package | Build Professional Websites in Minutes

[📚 Documentation](https://github.com/socialrabbit/bunny/docs) | [💬 Support](https://github.com/socialrabbit/bunny/discussions)

</div>

A powerful Laravel package for quickly scaffolding professional websites with multiple pre-built types and features.

## Features

- Multiple website types:
  - Portfolio
  - E-commerce
  - Educational
  - Healthcare
  - Hospitality
  - Real Estate

- Core Features:
  - Modern UI with responsive design
  - SEO optimization
  - Analytics integration
  - Media management
  - User authentication
  - Role-based permissions
  - Multi-language support
  - Theme customization
  - API integration
  - Performance optimization

## Requirements

- PHP >= 8.1
- Laravel >= 10.0
- Composer

## Installation

1. Install the package via Composer:
```bash
composer require socialrabbit/bunny
```

2. Run the installation command:
```bash
php artisan bunny:install
```

## Quick Start

1. Create a new website:
```bash
php artisan bunny:create {type}
```
Replace `{type}` with one of: portfolio, ecommerce, educational, healthcare, hospitality, real_estate

2. Configure your settings in `config/bunny.php`

3. Start customizing your website!

## Website Types

### Portfolio Website
- Project showcase
- Client testimonials
- Blog integration
- Contact forms
- Gallery management
- Resume builder
- Skills showcase
- Achievement timeline

### E-commerce Website
- Smart cart system
- Product management
- Order processing
- Inventory tracking
- Customer management
- Marketing tools
- Analytics dashboard
- Payment processing

### Educational Website
- Course management
- Student portal
- Assignment system
- Progress tracking
- Quiz system
- Resource library
- Discussion forums
- Certificate generation

### Healthcare Website
- Patient portal
- Appointment scheduling
- Medical records
- Prescription management
- Telemedicine integration
- Health blog
- Insurance verification
- Emergency contact

### Hospitality Website
- Room booking
- Event management
- Menu system
- Guest feedback
- Loyalty program
- Special offers
- Virtual tours
- Online check-in

### Real Estate Website
- Property listings
- Virtual tours
- Agent profiles
- Mortgage calculator
- Property search
- Saved searches
- Contact forms
- Market analysis

## Configuration

The package can be configured via the `config/bunny.php` file. Key configuration options include:

- Default website type
- Media storage settings
- Cache configuration
- Analytics integration
- SEO settings
- API configuration
- Theme customization

## Customization

### Themes
1. Publish theme assets:
```bash
php artisan vendor:publish --tag=bunny-assets
```

2. Customize CSS/JS in `public/vendor/bunny/`

### Views
1. Publish views:
```bash
php artisan vendor:publish --tag=bunny-views
```

2. Modify views in `resources/views/vendor/bunny/`

## API Documentation

The package provides a RESTful API for all features. Access the API at:
```
/api/bunny/{endpoint}
```

Full API documentation is available at [docs/API.md](docs/API.md)

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Support

- 📖 [Documentation](https://github.com/socialrabbit/bunny/docs)
- 💭 [Discussions](https://github.com/socialrabbit/bunny/discussions)
- 🐛 [Issues](https://github.com/socialrabbit/bunny/issues)
- 📧 [Email](mailto:iamsocialrabbit@gmail.com)

## Security

If you discover any security-related issues, please email iamsocialrabbit@gmail.com instead of using the issue tracker.

## Credits

- [Kisal Nelaka](https://github.com/kisalnelaka)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

<div align="center">

### 🌟 Star us on GitHub — it helps!

Made with ❤️ by [socialrabbit](https://github.com/socialrabbit)
</div>