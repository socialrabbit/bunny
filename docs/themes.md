# Theme Customization Guide

## Available Themes

Bunny comes with 5 professionally designed themes:

1. **Modern**
   - Clean and minimal design
   - Focus on content and whitespace
   - Perfect for professional portfolios

2. **Dark**
   - Rich dark theme with elegant gradients
   - High contrast for better readability
   - Great for creative portfolios

3. **Nature**
   - Organic colors and soft transitions
   - Calming visual experience
   - Ideal for environmental or artistic portfolios

4. **Retro**
   - Vintage-inspired design
   - Playful elements and typography
   - Perfect for creative and unique portfolios

5. **Neon**
   - Vibrant colors with glowing effects
   - High-impact visual design
   - Great for tech and gaming portfolios

## Theme Structure

Each theme consists of:

```
themes/
├── modern/
│   ├── css/
│   │   ├── main.css
│   │   └── variables.css
│   ├── js/
│   │   └── theme.js
│   └── assets/
│       └── images/
├── dark/
└── ...
```

## Customizing Themes

### 1. CSS Variables

Each theme uses CSS variables for easy customization:

```css
:root {
    --primary-color: #2563eb;
    --secondary-color: #3b82f6;
    --accent-color: #60a5fa;
    --text-color: #1f2937;
    --background-color: #ffffff;
    --card-background: #f8fafc;
    --border-color: #e2e8f0;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
```

### 2. Creating a Custom Theme

1. Create a new theme directory:
```bash
php artisan bunny:make:theme custom-theme
```

2. Configure your theme in `config/portfolio.php`:
```php
'themes' => [
    'available' => [
        'custom-theme' => [
            'name' => 'Custom Theme',
            'description' => 'Your custom theme description',
            'file' => 'custom-theme.css',
        ],
    ],
],
```

3. Customize your theme CSS:
```css
/* themes/custom-theme/css/main.css */
:root {
    --primary-color: #your-color;
    /* Other variables... */
}

/* Your custom styles... */
```

### 3. Theme Components

Each theme can have custom component styles:

```css
/* Card Component */
.portfolio-card {
    background: var(--card-background);
    border-radius: 0.5rem;
    overflow: hidden;
    transition: transform 0.3s ease;
}

/* Button Component */
.theme-button {
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
}
```

### 4. Responsive Design

All themes include responsive breakpoints:

```css
/* Mobile First Design */
.portfolio-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

/* Tablet */
@media (min-width: 640px) {
    .portfolio-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .portfolio-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
```

## Theme Switching

### 1. Using the Theme Switcher Component

```vue
<template>
  <theme-switcher :themes="availableThemes" />
</template>

<script>
export default {
  data() {
    return {
      availableThemes: [
        { name: 'modern', label: 'Modern' },
        { name: 'dark', label: 'Dark' },
        // Other themes...
      ]
    }
  }
}
</script>
```

### 2. Programmatic Theme Switching

```javascript
// Switch theme
function switchTheme(themeName) {
    document.documentElement.setAttribute('data-theme', themeName);
    localStorage.setItem('preferred-theme', themeName);
}

// Get current theme
function getCurrentTheme() {
    return localStorage.getItem('preferred-theme') || 'modern';
}
```

## Best Practices

1. **Color Accessibility**
   - Ensure sufficient contrast ratios
   - Test with color blindness simulators
   - Provide alternative color schemes

2. **Performance**
   - Minimize CSS file size
   - Use CSS variables for dynamic values
   - Implement lazy loading for images

3. **Browser Compatibility**
   - Test across major browsers
   - Provide fallbacks for modern CSS features
   - Use autoprefixer for vendor prefixes

4. **Maintenance**
   - Document custom variables and classes
   - Keep consistent naming conventions
   - Separate theme-specific and global styles

## Theme Migration

To migrate between themes:

```bash
php artisan bunny:theme:migrate old-theme new-theme
```

This will:
- Preserve custom configurations
- Transfer theme-specific assets
- Update database records
- Maintain user preferences

## Troubleshooting

Common theme issues and solutions:

1. **Theme not loading**
   - Check file permissions
   - Verify theme registration
   - Clear cache: `php artisan cache:clear`

2. **Inconsistent styles**
   - Check CSS specificity
   - Clear browser cache
   - Verify CSS variable definitions

3. **Mobile responsiveness**
   - Test on multiple devices
   - Use browser dev tools
   - Check media query breakpoints 