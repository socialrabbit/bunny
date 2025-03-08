<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portfolio Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your portfolio settings including themes,
    | layout options, and other customization features.
    |
    */

    'themes' => [
        'default' => 'modern',
        'available' => [
            'modern' => [
                'name' => 'Modern',
                'description' => 'Clean and minimal design with focus on content',
                'file' => 'modern.css',
            ],
            'dark' => [
                'name' => 'Dark',
                'description' => 'Rich dark theme with elegant gradients',
                'file' => 'dark.css',
            ],
            'nature' => [
                'name' => 'Nature',
                'description' => 'Organic colors and soft transitions',
                'file' => 'nature.css',
            ],
            'retro' => [
                'name' => 'Retro',
                'description' => 'Vintage-inspired design with playful elements',
                'file' => 'retro.css',
            ],
            'neon' => [
                'name' => 'Neon',
                'description' => 'Vibrant colors with glowing effects',
                'file' => 'neon.css',
            ],
        ],
    ],

    'layout' => [
        'grid' => [
            'columns' => [
                'desktop' => 3,
                'tablet' => 2,
                'mobile' => 1,
            ],
            'gap' => '2rem',
        ],
        'card' => [
            'aspect_ratio' => '3/2',
            'image_height' => '200px',
            'hover_effect' => true,
        ],
    ],

    'features' => [
        'tags' => true,
        'categories' => true,
        'search' => true,
        'filter' => true,
        'pagination' => true,
        'theme_switcher' => true,
        'resume' => [
            'enabled' => true,
            'max_file_size' => 10240, // 10MB in kilobytes
            'allowed_formats' => ['pdf', 'doc', 'docx'],
            'track_downloads' => true,
            'analytics' => [
                'enabled' => true,
                'track_unique_downloads' => true,
                'track_referrers' => true,
            ],
            'storage' => [
                'disk' => 'public',
                'path' => 'resumes',
            ],
            'display' => [
                'show_download_count' => true,
                'show_file_size' => true,
                'show_last_updated' => true,
                'show_highlights' => true,
            ],
        ],
    ],

    'meta' => [
        'title' => 'My Portfolio',
        'description' => 'A showcase of my work and projects',
        'keywords' => 'portfolio, projects, work',
        'author' => config('app.name'),
    ],

    'cache' => [
        'enabled' => true,
        'duration' => 3600, // 1 hour
    ],
]; 