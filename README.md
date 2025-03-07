# Bunny - Laravel Scaffolding Package

Bunny is a powerful Laravel scaffolding package that helps you quickly generate modern web applications with your preferred frontend framework and UI library.

## Features

### Portfolio System
- **Project Management**
  - Category and tag organization
  - Featured projects highlighting
  - Custom ordering and sorting
  - Image galleries with optimization
  - Responsive image handling
  - SEO-friendly URLs
- **About Section**
  - Skills visualization
  - Experience timeline
  - Education history
  - Dynamic content management
- **Contact Integration**
  - Form with validation
  - Mail notifications
  - Google Maps integration
  - Social media links
- **Advanced Features**
  - Dark mode support
  - Image optimization
  - Multiple image formats (webp, jpg)
  - SEO meta tags
  - Analytics integration
  - Cache management
  - Responsive design

### Frontend Framework Support
- **Vue.js 3**
  - Composition API
  - Single File Components
  - Modern UI components
- **React 18**
  - Functional Components
  - Hooks support
  - JSX templates
- **Alpine.js 3**
  - Lightweight interactivity
  - No build step required
  - Perfect for simple applications

### UI Libraries
- **Tailwind CSS**
  - Utility-first CSS
  - Responsive design
  - Custom components
- **Bootstrap 5**
  - Grid system
  - Components
  - Utilities
- **Custom CSS**
  - Framework-agnostic styles
  - Modern animations
  - Responsive layouts

### API Integration
- **REST API**
  - Resource controllers
  - Form requests
  - API resources
  - Route model binding
- **GraphQL** (Coming Soon)
  - Schema generation
  - Type definitions
  - Resolvers

### Website Types
- **Portfolio**
  - Project showcase
  - Image galleries
  - Contact forms
- **E-commerce**
  - Product management
  - Shopping cart
  - Order processing
- **CMS**
  - Content management
  - User roles
  - Media handling

## Installation

1. Create a new Laravel project:
```bash
composer create-project laravel/laravel example-app
cd example-app
```

2. Install Bunny:
```bash
composer require bunny/bunny
```

3. Run the installation command:
```bash
php artisan bunny:install
```

4. Follow the interactive prompts to:
   - Select your frontend framework
   - Choose a UI library
   - Pick a website type
   - Configure API settings

## Portfolio Configuration

Customize your portfolio in `config/bunny.php`:

```php
return [
    'sections' => [
        'projects' => [
            'enabled' => true,
            'items_per_page' => 12,
            'categories_enabled' => true,
            'tags_enabled' => true,
            'search_enabled' => true,
            'filter_enabled' => true,
        ],
        // ... other sections
    ],
    'features' => [
        'dark_mode' => true,
        'animations' => true,
        'image_optimization' => true,
        'seo' => true,
        // ... other features
    ],
    // ... additional configuration
];
```

## Portfolio Components

### Project Grid
```php
<x-portfolio.projects-grid
    :items="$projects"
    :categories="$categories"
    :tags="$tags"
/>
```

### Skills Section
```php
<x-portfolio.skills
    :skills="$skills"
    :categories="$categories"
/>
```

### Contact Form
```php
<x-portfolio.contact-form
    :social-links="$socialLinks"
    :map-enabled="true"
/>
```

## Media Handling

The portfolio system includes advanced media handling:

```php
// In your Project model
public function registerMediaCollections(): void
{
    $this->addMediaCollection('images')
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
        ->withResponsiveImages();

    $this->addMediaCollection('thumbnail')
        ->singleFile()
        ->withResponsiveImages();
}
```

## Usage

### Generate Components

```bash
# Generate frontend components
php artisan bunny:frontend

# Generate backend components
php artisan bunny:backend

# Generate API components
php artisan bunny:api
```

### Directory Structure

```
your-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Portfolio/
│   │   │   ├── Api/
│   │   │   └── Web/
│   │   └── Requests/
│   │       └── Api/
│   └── Models/
├── resources/
│   ├── js/
│   │   └── components/
│   │       ├── portfolio/
│   │       ├── vue/
│   │       ├── react/
│   │       └── alpine/
│   └── views/
│       └── components/
└── routes/
    ├── api.php
    └── web.php
```

### Frontend Components

#### Vue.js Example
```vue
<template>
  <div class="portfolio-container">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="item in items" :key="item.id" class="portfolio-item">
        <!-- Component content -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PortfolioList',
  data() {
    return {
      items: []
    }
  },
  async created() {
    // Fetch data
  }
}
</script>
```

#### React Example
```jsx
import React, { useState, useEffect } from 'react';

const PortfolioList = () => {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch data
  }, []);

  return (
    <div className="container mx-auto px-4 py-8">
      {/* Component content */}
    </div>
  );
};
```

#### Alpine.js Example
```javascript
document.addEventListener('alpine:init', () => {
  Alpine.data('portfolioList', () => ({
    items: [],
    loading: true,

    async init() {
      // Fetch data
    }
  }));
});
```

### API Integration

#### REST API Example
```php
// Controller
class PortfolioController extends Controller
{
    public function index()
    {
        $items = Portfolio::latest()->paginate(12);
        return PortfolioResource::collection($items);
    }
}

// Resource
class PortfolioResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            // ...
        ];
    }
}
```

## Configuration

Publish the configuration file:
```bash
php artisan vendor:publish --tag=bunny-config
```

Edit `config/bunny.php`:
```php
return [
    'frontend' => [
        'framework' => 'vue', // vue, react, or alpine
        'ui_library' => 'tailwind', // tailwind or bootstrap
    ],
    'api' => [
        'type' => 'rest', // rest or graphql
        'version' => 'v1',
    ],
    // ...
];
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, please open an issue in the GitHub repository or contact the maintainers.

## Credits

- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [React](https://reactjs.org)
- [Alpine.js](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [Bootstrap](https://getbootstrap.com)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) 