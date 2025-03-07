<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portfolio Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the portfolio section
    | of your website. Customize these values according to your needs.
    |
    */

    'sections' => [
        'projects' => [
            'enabled' => true,
            'items_per_page' => 12,
            'categories_enabled' => true,
            'tags_enabled' => true,
            'search_enabled' => true,
            'filter_enabled' => true,
        ],
        'about' => [
            'enabled' => true,
            'skills_enabled' => true,
            'experience_enabled' => true,
            'education_enabled' => true,
        ],
        'contact' => [
            'enabled' => true,
            'form_enabled' => true,
            'social_links_enabled' => true,
            'map_enabled' => true,
        ],
    ],

    'features' => [
        'dark_mode' => true,
        'animations' => true,
        'image_optimization' => true,
        'seo' => true,
        'analytics' => true,
        'cache' => true,
    ],

    'meta' => [
        'title' => 'My Portfolio',
        'description' => 'Welcome to my professional portfolio',
        'keywords' => 'portfolio, projects, skills, experience',
        'author' => 'Your Name',
        'og_image' => 'images/og-image.jpg',
    ],

    'social' => [
        'github' => [
            'enabled' => true,
            'url' => 'https://github.com/yourusername',
        ],
        'linkedin' => [
            'enabled' => true,
            'url' => 'https://linkedin.com/in/yourusername',
        ],
        'twitter' => [
            'enabled' => true,
            'url' => 'https://twitter.com/yourusername',
        ],
    ],

    'analytics' => [
        'google' => [
            'enabled' => true,
            'tracking_id' => 'G-XXXXXXXXXX',
        ],
    ],

    'cache' => [
        'duration' => 3600, // Cache duration in seconds
        'projects' => true,
        'skills' => true,
        'experience' => true,
    ],

    'images' => [
        'thumbnails' => [
            'width' => 400,
            'height' => 300,
            'quality' => 80,
        ],
        'full' => [
            'max_width' => 1920,
            'max_height' => 1080,
            'quality' => 90,
        ],
        'formats' => ['webp', 'jpg'],
    ],
]; 