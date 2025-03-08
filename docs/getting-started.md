# Getting Started with Bunny

## Prerequisites

- PHP >= 8.1
- Laravel >= 10.0
- Composer
- Node.js >= 16.0
- NPM or Yarn

## Installation

1. Create a new Laravel project (skip if you have an existing project):
```bash
composer create-project laravel/laravel my-portfolio
cd my-portfolio
```

2. Install Bunny via Composer:
```bash
composer require socialrabbit/bunny
```

3. Run the installation command:
```bash
php artisan bunny:install
```

4. Follow the interactive prompts to configure:
   - Frontend Framework (Vue.js, React, or Alpine.js)
   - UI Library (Tailwind CSS or Bootstrap)
   - Website Type (Portfolio, E-commerce, or CMS)
   - API Type (REST or GraphQL)

5. Publish the configuration:
```bash
php artisan vendor:publish --tag=bunny-config
```

6. Install frontend dependencies:
```bash
npm install
# or
yarn install
```

7. Build assets:
```bash
npm run dev
# or
yarn dev
```

## Basic Configuration

### Environment Variables

Add these to your `.env` file:

```env
BUNNY_THEME=modern
BUNNY_ANALYTICS_ENABLED=true
BUNNY_CACHE_DURATION=3600
```

### Theme Configuration

Edit `config/portfolio.php`:

```php
return [
    'themes' => [
        'default' => 'modern',
        'available' => [
            'modern' => [
                'name' => 'Modern',
                'file' => 'modern.css',
            ],
            // Other themes...
        ],
    ],
];
```

## Quick Start Templates

### Portfolio Website

1. Generate portfolio components:
```bash
php artisan bunny:make:portfolio
```

2. Configure your portfolio in `config/portfolio.php`:
```php
return [
    'meta' => [
        'title' => 'Your Name - Portfolio',
        'description' => 'Professional portfolio showcasing my work',
    ],
    'sections' => [
        'projects' => true,
        'about' => true,
        'contact' => true,
        'resume' => true,
    ],
];
```

3. Add your first project:
```php
use Bunny\Models\Project;

Project::create([
    'title' => 'My Awesome Project',
    'description' => 'Project description',
    'url' => 'https://project-url.com',
    'image' => 'path/to/image.jpg',
]);
```

### Resume Section

1. Enable the resume feature:
```php
'features' => [
    'resume' => [
        'enabled' => true,
        // Other resume settings...
    ],
],
```

2. Upload your resume via the API or dashboard.

## Next Steps

- [Theme Customization](./themes.md)
- [Component Development](./components.md)
- [API Integration](./api.md)
- [Deployment Guide](./deployment.md) 