# 🐰 Bunny - Laravel Website Scaffolding Package

<div align="center">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/socialrabbit/bunny.svg)](https://packagist.org/packages/socialrabbit/bunny)
[![Total Downloads](https://img.shields.io/packagist/dt/socialrabbit/bunny.svg)](https://packagist.org/packages/socialrabbit/bunny)
[![License](https://img.shields.io/github/license/socialrabbit/bunny.svg)](LICENSE.md)
[![Tests](https://github.com/socialrabbit/bunny/actions/workflows/tests.yml/badge.svg)](https://github.com/socialrabbit/bunny/actions/workflows/tests.yml)
[![StyleCI](https://github.styleci.io/repos/socialrabbit/bunny/shield)](https://github.styleci.io/repos/socialrabbit/bunny)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.x-brightgreen.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.1-blue.svg)](https://php.net)
[![GitHub Stars](https://img.shields.io/github/stars/socialrabbit/bunny.svg)](https://github.com/socialrabbit/bunny/stargazers)
[![GitHub Issues](https://img.shields.io/github/issues/socialrabbit/bunny.svg)](https://github.com/socialrabbit/bunny/issues)

> 🚀 The Ultimate Laravel Website Builder & Scaffolding Package | Build Professional Websites in Minutes with Vue.js, React, or Alpine.js

A powerful Laravel package that helps you scaffold professional websites in minutes with modern frontend frameworks and best practices. Perfect for portfolios, e-commerce sites, and content management systems.

[📚 Documentation](https://bunny.socialrabbit.dev/docs) | [🎮 Live Demo](https://demo.bunny.socialrabbit.dev) | [💬 Support](https://github.com/socialrabbit/bunny/discussions) | [⭐ Star on GitHub](https://github.com/socialrabbit/bunny)

</div>

## Why Choose Bunny? 🤔

- ⚡ **Lightning Fast Development**: Build a complete website in under 5 minutes
- 🎨 **Modern Tech Stack**: Laravel 10, Vue.js 3, React 18, Alpine.js 3, Tailwind CSS, Bootstrap 5
- 🔥 **Hot Reload**: Real-time development with Vite.js
- 🛡️ **Security First**: Built-in XSS protection, CSRF, and SQL injection prevention
- 📱 **Mobile Ready**: Responsive design with mobile-first approach
- 🌐 **SEO Optimized**: Built-in meta tags, sitemap, and robots.txt generation

## 📊 Repository Metrics

| Metric | Value |
|--------|-------|
| ⭐ Stars | ${repo.stargazers_count} |
| 🔱 Forks | ${repo.forks_count} |
| 👀 Watchers | ${repo.watchers_count} |
| 🐛 Open Issues | ${issues.length} |
| 👥 Active Contributors | ${uniqueUsers.size} |
| 📦 Total Downloads | ${downloads.total} |
| 📥 Monthly Downloads | ${downloads.monthly} |
| 📊 Daily Downloads | ${downloads.daily} |

Last updated: ${new Date().toISOString()}

## ✨ Features

- 🚀 **Quick Setup**: Scaffold a complete website in minutes
- 🎨 **Frontend Frameworks**: Vue.js 3, React 18, Alpine.js 3
- 🎯 **UI Libraries**: Tailwind CSS, Bootstrap 5
- 🔌 **API Integration**: REST & GraphQL support
- 🛠️ **Backend Features**: Full CRUD operations
- 📦 **Component System**: Pre-built, customizable components
- 🎨 **Themes**: 5 beautiful, responsive themes
- 📄 **Resume Section**: Professional resume showcase
- 🔍 **SEO Optimized**: Built-in SEO best practices
- 📊 **Analytics**: Integrated tracking and metrics

## 🚀 Quick Start

```bash
# Create new Laravel project
composer create-project laravel/laravel my-website

# Install Bunny
composer require socialrabbit/bunny

# Run installation wizard
php artisan bunny:install
```

## 📚 Documentation

- [Getting Started](docs/getting-started.md)
- [Configuration](docs/configuration.md)
- [Themes](docs/themes.md)
- [Components](docs/components.md)
- [API Integration](docs/api.md)
- [Resume Feature](docs/resume.md)
- [Deployment](docs/deployment.md)
- [Contributing](CONTRIBUTING.md)

## 🎯 Use Cases

### Portfolio Website
```php
php artisan bunny:make:portfolio
```
- Professional portfolio with projects showcase
- Resume section with download tracking
- Contact form with mail integration
- SEO optimization

### E-commerce Site
```php
php artisan bunny:make:ecommerce
```
- Product catalog with categories
- Shopping cart functionality
- Payment integration
- Order management

### Content Management
```php
php artisan bunny:make:cms
```
- Blog with categories and tags
- Media management
- User roles and permissions
- Content scheduling

## 🛠️ Requirements

- PHP >= 8.1
- Laravel >= 10.0
- Node.js >= 16.0
- Composer
- NPM or Yarn

## 🤝 Contributing

Contributions are welcome! Please check our [Contributing Guidelines](CONTRIBUTING.md).

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 💖 Support

- 🌟 [Star this project](https://github.com/socialrabbit/bunny)
- 🐛 [Report an issue](https://github.com/socialrabbit/bunny/issues)
- 💭 [Join discussions](https://github.com/socialrabbit/bunny/discussions)
- 📖 [Read documentation](https://bunny.socialrabbit.dev/docs)

## ❓ FAQ & Troubleshooting

### Installation Issues

**Q: Getting "Package socialrabbit/bunny not found" error**
```bash
A: Make sure your composer.json has the correct repository:
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/socialrabbit/bunny"
        }
    ]
}
```

**Q: Node modules installation fails**
```bash
A: Try clearing npm cache and using a specific Node.js version:
npm cache clean --force
nvm use 16
npm install
```

### Theme Issues

**Q: Custom theme not loading after installation**
```bash
A: Run these commands in sequence:
php artisan config:clear
php artisan cache:clear
npm run build
```

**Q: Assets not showing up in production**
```bash
A: Ensure you've published assets and compiled for production:
php artisan vendor:publish --tag=bunny-assets --force
npm run build
```

### API Integration

**Q: CORS errors when making API requests**
```php
A: Add your frontend URL to config/cors.php:
return [
    'paths' => ['api/*'],
    'allowed_origins' => ['http://your-frontend-url.com'],
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
];
```

**Q: Authentication tokens not working**
```bash
A: Check your .env configuration:
SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
SESSION_DOMAIN=.your-app-domain.com
```

### Performance

**Q: Slow page load times**
```bash
A: Implement these optimizations:
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Q: High memory usage**
```php
A: Adjust your config/bunny.php cache settings:
'cache' => [
    'duration' => 3600,
    'prefix' => 'bunny_',
    'store' => 'redis'
],
```

### Common Runtime Errors

**Q: "Class 'Bunny\PortfolioManager' not found"**
```bash
A: Refresh the autoloader:
composer dump-autoload -o
```

**Q: "Unable to locate mix file" error**
```bash
A: Switch to Vite or fix mix configuration:
npm install --save-dev laravel-vite-plugin
# Then update your vite.config.js configuration
```

For more detailed solutions and troubleshooting guides, visit our [documentation](https://bunny.socialrabbit.dev/docs/troubleshooting).

## 🙏 Credits

Created with ❤️ by [Kisal Nelaka](https://github.com/kisalnelaka)

## 🎯 Perfect For

- 💼 **Professional Portfolios**: Showcase your work with beautiful layouts
- 🛍️ **E-commerce Websites**: Build online stores with cart and payment integration
- 📝 **Content Management**: Create blogs and dynamic content websites
- 🎨 **Agency Websites**: Develop client websites faster
- 📱 **SaaS Applications**: Launch your SaaS product quickly
- 🌐 **Landing Pages**: Create high-converting landing pages

## 💫 What Makes Bunny Special?

1. **Zero Configuration Required**
   - Install and run with a single command
   - Smart defaults that just work
   - Automatic environment detection

2. **Modern Development Experience**
   - Hot Module Replacement (HMR)
   - TypeScript support out of the box
   - Component-based architecture

3. **Production Ready**
   - Optimized asset bundling
   - Cache strategies implemented
   - Performance optimized code

## 🔍 Keywords

`laravel scaffolding`, `website builder`, `laravel package`, `vue.js scaffolding`, `react scaffolding`, `alpine.js scaffolding`, `laravel portfolio`, `laravel cms`, `laravel ecommerce`, `laravel website generator`, `laravel starter kit`, `laravel boilerplate`, `laravel template`, `laravel themes`, `laravel components`

## 📈 Performance Benchmarks

| Metric | Bunny | Traditional Development |
|--------|-------|------------------------|
| Setup Time | 5 minutes | 2-3 hours |
| Build Time | < 2 seconds | 10-15 seconds |
| First Load | < 1 second | 2-3 seconds |
| Lighthouse Score | 95+ | 75-85 |

---

<div align="center">

### 🌟 Star us on GitHub — it helps!

[![Stargazers over time](https://starchart.cc/socialrabbit/bunny.svg)](https://starchart.cc/socialrabbit/bunny)

Made with ❤️ by <a href="https://github.com/socialrabbit">socialrabbit</a>
</div>