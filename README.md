# Bunny - Laravel Scaffolding Made Easy

Bunny is a Laravel package that provides easy scaffolding for common website types like portfolios and e-commerce sites. It helps you quickly set up a new website with all the necessary boilerplate code and best practices.

## Prerequisites

- PHP 8.1 or higher
- Laravel 10.x or higher
- Composer
- Node.js and NPM (for frontend assets)
- A database (MySQL, PostgreSQL, or SQLite)

## Installation

1. Create a new Laravel project (skip if you have an existing project):
```bash
composer create-project laravel/laravel your-project-name
cd your-project-name
```

2. Install the Bunny package via composer:
```bash
composer require socialrabbit/bunny
```

3. Publish the configuration file:
```bash
php artisan vendor:publish --provider="Bunny\BunnyServiceProvider"
```

4. Set up your database credentials in the `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Quick Start Guide

1. Run the Bunny installation command:
```bash
php artisan bunny:install
```

2. Follow the interactive prompts:
   - Choose website type (portfolio/e-commerce)
   - Enter model name (e.g., Project for portfolio, Product for e-commerce)
   - Define model fields (e.g., title:string, description:text, price:decimal)
   - Select frontend framework (Vue.js, React, Alpine.js, or none)
   - Choose API type (REST or GraphQL)
   - Select UI library (Tailwind CSS, Bootstrap, or none)
   - Select optional features:
     - Authentication
     - Payment integration (e-commerce only)
     - Additional packages
     - CMS functionality

3. After installation completes, run migrations:
```bash
php artisan migrate
```

4. Start the development server:
```bash
php artisan serve
```

5. Visit your website at `http://localhost:8000`

## Frontend Framework Support

Bunny provides comprehensive frontend framework support with modern tooling and best practices.

### Supported Frameworks

#### Vue.js 3
```vue
<template>
  <div class="container">
    <h1>{{ modelName }} Portfolio</h1>
    <div class="grid">
      <div v-for="item in items" :key="item.id" class="card">
        <h2>{{ item.title }}</h2>
        <p>{{ item.description }}</p>
        <button @click="viewDetails(item)">View Details</button>
      </div>
    </div>
  </div>
</template>
```

#### React 18
```jsx
const {{ modelName }}List = () => {
  const [items, setItems] = useState([]);
  
  return (
    <div className="container">
      <h1>{{ modelName }} Portfolio</h1>
      <div className="grid">
        {items.map(item => (
          <div key={item.id} className="card">
            <h2>{item.title}</h2>
            <p>{item.description}</p>
            <button onClick={() => viewDetails(item)}>View Details</button>
          </div>
        ))}
      </div>
    </div>
  );
};
```

#### Alpine.js 3
```js
export default function {{ modelName }}List() {
  return {
    items: [],
    async init() {
      const response = await fetch('/api/{{ modelName.toLowerCase() }}s');
      this.items = await response.json();
    }
  };
}
```

### UI Libraries

#### Tailwind CSS
```html
<div class="container mx-auto px-4">
  <h1 class="text-3xl font-bold mb-6">{{ modelName }} Portfolio</h1>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Content -->
  </div>
</div>
```

#### Bootstrap 5
```html
<div class="container">
  <h1 class="display-4 mb-4">{{ modelName }} Portfolio</h1>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <!-- Content -->
  </div>
</div>
```

### API Integration

#### REST API
```php
// Generated API Controller
class {{ modelName }}Controller extends Controller
{
    public function index()
    {
        return {{ modelName }}::all();
    }
}
```

#### GraphQL (Coming Soon)
```graphql
type {{ modelName }} {
    id: ID!
    title: String!
    description: String
    # ... other fields
}
```

### Component Generation
The package automatically generates:
- List components with pagination
- Detail components with data fetching
- Form components with validation
- Navigation components with routing
- Layout templates with responsive design
- API integration components
- Authentication components (if enabled)

## Directory Structure

After scaffolding, your project will have the following structure:

```
your-project/
├── app/
│   ├── Http/Controllers/         # Generated controllers
│   │   ├── PageController.php    # CMS controller (if selected)
│   │   └── YourController.php    # Main feature controller
│   └── Models/                   # Generated models
├── database/
│   ├── migrations/              # Database migrations
│   ├── factories/              # Model factories
│   └── seeders/               # Database seeders
├── resources/
│   ├── js/
│   │   ├── components/        # Frontend components
│   │   │   ├── vue/         # Vue components (if selected)
│   │   │   ├── react/      # React components (if selected)
│   │   │   └── alpine/    # Alpine.js components (if selected)
│   │   └── app.js         # Main JavaScript entry
│   └── views/                 # View templates
│       ├── portfolio/        # Portfolio templates (if selected)
│       ├── ecommerce/       # E-commerce templates (if selected)
│       └── cms/            # CMS templates (if selected)
└── routes/
    ├── web.php              # Web routes
    └── api.php              # API routes (if frontend framework selected)
```

## Feature Guides

### Portfolio Website
If you selected the portfolio type, you'll get:
- Project listing page with frontend components
- Project detail pages with dynamic routing
- Contact form with validation
- About page with CMS integration
- Admin dashboard (if CMS selected)

Access your portfolio at:
- Home: `http://localhost:8000`
- Projects: `http://localhost:8000/projects`
- Admin: `http://localhost:8000/admin` (if CMS enabled)

### E-commerce Website
If you selected the e-commerce type, you'll get:
- Product catalog with filtering
- Shopping cart with real-time updates
- Checkout process with payment integration
- Order management system
- Admin dashboard with inventory management

Access your store at:
- Home: `http://localhost:8000`
- Products: `http://localhost:8000/products`
- Cart: `http://localhost:8000/cart`
- Admin: `http://localhost:8000/admin` (if CMS enabled)

### CMS Features
If you enabled CMS functionality:
1. Access the admin panel at `/admin`
2. Manage pages at `/admin/pages`
3. Create and edit content with the built-in editor
4. Manage meta tags and SEO settings
5. Frontend preview functionality

### Authentication
If you enabled authentication:
- Login: `http://localhost:8000/login`
- Register: `http://localhost:8000/register`
- Password Reset: `http://localhost:8000/password/reset`
- Social Login (if configured)

### Payment Integration
If you enabled payment integration for e-commerce:

1. Set up your payment credentials in `.env`:
```env
STRIPE_KEY=your_publishable_key
STRIPE_SECRET=your_secret_key
```

2. Configure webhook endpoints (if using Stripe):
```bash
php artisan stripe:webhook
```

## Configuration

You can customize the package behavior in `config/bunny.php`:

```php
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
    'optional_packages' => [
        'socialite'  => 'laravel/socialite',
        'sanctum'    => 'laravel/sanctum',
        'permission' => 'spatie/laravel-permission',
        'debugbar'   => 'barryvdh/laravel-debugbar',
        'inertia'    => 'inertiajs/inertia-laravel',
        'livewire'   => 'livewire/livewire',
    ],
];
```

## Customization

### Templates
All views are published to your resources directory and can be customized:
1. Portfolio templates: `resources/views/portfolio/`
2. E-commerce templates: `resources/views/ecommerce/`
3. CMS templates: `resources/views/cms/`
4. Frontend components: `resources/js/components/`

### Styles
The package uses Tailwind CSS by default. To customize:
1. Install dependencies:
```bash
npm install
```

2. Modify the Tailwind configuration:
```bash
npx tailwindcss init
```

3. Compile assets:
```bash
npm run dev
```

## Troubleshooting

### Common Issues

1. **Migrations fail to run:**
   - Check database credentials in `.env`
   - Ensure database exists
   - Run `php artisan config:clear`

2. **Pages not found (404):**
   - Run `php artisan route:clear`
   - Check `routes/web.php` for conflicts
   - Verify .htaccess configuration

3. **Assets not loading:**
   - Run `npm install && npm run dev`
   - Check public directory permissions
   - Clear browser cache

4. **Frontend framework issues:**
   - Ensure Node.js and NPM are installed
   - Run `npm install` in project root
   - Check framework-specific configuration files
   - Clear browser cache and node_modules
   - Verify Vite configuration (if using Vue/React)

### Getting Help

If you encounter issues:
1. Check the Laravel logs in `storage/logs`
2. Run `php artisan route:list` to verify routes
3. Enable debug mode in `.env`: `APP_DEBUG=true`
4. Check browser console for frontend errors

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## Security

If you discover any security-related issues, please email kisalnelaka6@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information. 
