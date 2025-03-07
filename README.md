# Bunny - Laravel Website Scaffolding Package

A powerful Laravel package that helps you quickly scaffold different types of websites with modern frontend frameworks and best practices.

## 📊 Repository Metrics

| Metric | Value |
|--------|-------|
| ⭐ Stars | 1 |
| 🔱 Forks | 0 |
| 👀 Watchers | 1 |
| 🐛 Open Issues | 0 |
| 👥 Active Contributors | 1 |
| 📦 Total Downloads | 0 |
| 📥 Monthly Downloads | 0 |
| 📊 Daily Downloads | 0 |

Last updated: 2025-03-07T14:09:58.424Z
## Features

- 🚀 Quick website scaffolding for Portfolio, E-commerce, and CMS
- 🎨 Frontend Framework Support:
  - Vue.js 3 with Composition API
  - React 18 with Hooks
  - Alpine.js 3 for lightweight interactivity
- 🎯 UI Library Support:
  - Tailwind CSS
  - Bootstrap 5
- 🔌 API Integration:
  - REST API support
  - API Resource generation
  - Form Request validation
  - API testing
- 🛠️ Backend Features:
  - Model generation with fillable and casts
  - Controller generation with CRUD operations
  - Migration generation
  - Blade view templates
  - Route registration
- 📦 Component Generation:
  - Frontend components with modern UI
  - Responsive design
  - Loading states
  - Error handling
  - Form validation
  - Data fetching
- 🔄 Development Tools:
  - Interactive CLI commands
  - Stub customization
  - Configuration management
  - Asset publishing
- ⭐ GitHub Integration:
  - Repository starring during installation
  - Repository statistics display
  - GitHub token support
  - Cached repository stats

## Installation

1. Create a new Laravel project:
```bash
composer create-project laravel/laravel my-project
cd my-project
```

2. Install Bunny:
```bash
composer require socialrabbit/bunny
```

3. Run the installation command:
```bash
php artisan bunny:install
```

The installation process will:
- Show repository statistics
- Ask if you want to star the repository
- Guide you through selecting your preferred:
  - Frontend framework
  - UI library
  - Website type
  - API type

### Installation Options

- Force reinstall:
```bash
php artisan bunny:install --force
```

- With GitHub token:
```bash
php artisan bunny:install --github-token=your-token
```

## Quick Start

1. Choose your website type:
```bash
php artisan bunny:install
```

2. Select your preferences:
   - Frontend Framework (Vue.js, React, Alpine.js)
   - UI Library (Tailwind CSS, Bootstrap)
   - Website Type (Portfolio, E-commerce, CMS)
   - API Type (REST, GraphQL)

3. Start developing!

## Frontend Framework Support

### Vue.js 3
```vue
<template>
  <div class="portfolio-grid">
    <div v-for="project in projects" :key="project.id" class="project-card">
      <img :src="project.image" :alt="project.title">
      <h3>{{ project.title }}</h3>
      <p>{{ project.description }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const projects = ref([])

onMounted(async () => {
  const response = await fetch('/api/projects')
  projects.value = await response.json()
})
</script>
```

### React 18
```jsx
import { useState, useEffect } from 'react'

function ProjectGrid() {
  const [projects, setProjects] = useState([])

  useEffect(() => {
    fetch('/api/projects')
      .then(res => res.json())
      .then(data => setProjects(data))
  }, [])

  return (
    <div className="portfolio-grid">
      {projects.map(project => (
        <div key={project.id} className="project-card">
          <img src={project.image} alt={project.title} />
          <h3>{project.title}</h3>
          <p>{project.description}</p>
        </div>
      ))}
    </div>
  )
}
```

### Alpine.js 3
```html
<div x-data="{ projects: [] }" x-init="fetch('/api/projects').then(res => res.json()).then(data => projects = data)">
  <div class="portfolio-grid">
    <template x-for="project in projects" :key="project.id">
      <div class="project-card">
        <img :src="project.image" :alt="project.title">
        <h3 x-text="project.title"></h3>
        <p x-text="project.description"></p>
      </div>
    </template>
  </div>
</div>
```

## API Integration

### REST API
```php
// Generated API Resource
class ProjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image_url,
            'created_at' => $this->created_at,
        ];
    }
}

// Generated Form Request
class StoreProjectRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
        ];
    }
}
```

## Directory Structure

```
my-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ProjectController.php
│   │   └── Requests/
│   │       └── StoreProjectRequest.php
│   └── Models/
│       └── Project.php
├── resources/
│   ├── js/
│   │   └── components/
│   │       └── portfolio/
│   │           └── ProjectGrid.vue
│   └── views/
│       └── portfolio/
│           └── index.blade.php
└── routes/
    ├── web.php
    └── api.php
```

## Configuration

Publish the configuration file:
```bash
php artisan vendor:publish --provider="Bunny\BunnyServiceProvider" --tag="bunny-config"
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Support

- Documentation: [https://github.com/socialrabbit/bunny/wiki](https://github.com/socialrabbit/bunny/wiki)
- Issues: [https://github.com/socialrabbit/bunny/issues](https://github.com/socialrabbit/bunny/issues)
- Discussions: [https://github.com/socialrabbit/bunny/discussions](https://github.com/socialrabbit/bunny/discussions)

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

Created by [Kisal Nelaka](https://github.com/kisalnelaka) for [socialrabbit](https://github.com/socialrabbit)