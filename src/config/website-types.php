<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Website Types Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for all available website types
    | in the Bunny package. Each website type has its own set of features
    | and dependencies that will be installed when the type is selected.
    |
    */

    'types' => [
        'ecommerce' => [
            'name' => 'E-commerce',
            'description' => 'Online store with product management and payment processing',
            'icon' => 'shopping-cart',
            'color' => '#4CAF50',
            'features' => [
                'smart_cart',
                'product_management',
                'order_processing',
                'inventory_tracking',
                'customer_management',
                'marketing_tools',
                'analytics_dashboard',
                'payment_processing',
            ],
            'dependencies' => [
                'auth',
                'roles',
                'media',
                'notifications',
                'payment',
                'shipping',
                'tax',
                'inventory',
            ],
        ],
        'portfolio' => [
            'name' => 'Portfolio',
            'description' => 'Personal or professional portfolio website',
            'icon' => 'briefcase',
            'color' => '#2196F3',
            'features' => [
                'project_showcase',
                'client_testimonials',
                'blog_integration',
                'contact_forms',
                'gallery_management',
                'resume_builder',
                'skills_showcase',
                'achievement_timeline',
            ],
            'dependencies' => [
                'auth',
                'media',
                'notifications',
                'seo',
            ],
        ],
        'educational' => [
            'name' => 'Educational',
            'description' => 'Educational platform with courses and learning management',
            'icon' => 'graduation-cap',
            'color' => '#9C27B0',
            'features' => [
                'course_management',
                'student_portal',
                'assignment_system',
                'progress_tracking',
                'quiz_system',
                'resource_library',
                'discussion_forums',
                'certificate_generation',
            ],
            'dependencies' => [
                'auth',
                'roles',
                'media',
                'notifications',
                'payment',
                'calendar',
                'chat',
                'analytics',
            ],
        ],
        'healthcare' => [
            'name' => 'Healthcare',
            'description' => 'Healthcare website with patient portal and appointment scheduling',
            'icon' => 'heartbeat',
            'color' => '#F44336',
            'features' => [
                'patient_portal',
                'appointment_scheduling',
                'medical_records',
                'prescription_management',
                'telemedicine_integration',
                'health_blog',
                'insurance_verification',
                'emergency_contact',
            ],
            'dependencies' => [
                'auth',
                'roles',
                'media',
                'notifications',
                'calendar',
                'chat',
                'payment',
                'analytics',
            ],
        ],
        'hospitality' => [
            'name' => 'Hospitality',
            'description' => 'Hotel and hospitality management system',
            'icon' => 'bed',
            'color' => '#FF9800',
            'features' => [
                'room_booking',
                'event_management',
                'menu_system',
                'guest_feedback',
                'loyalty_program',
                'special_offers',
                'virtual_tours',
                'online_checkin',
            ],
            'dependencies' => [
                'auth',
                'roles',
                'media',
                'notifications',
                'payment',
                'calendar',
                'chat',
                'analytics',
            ],
        ],
        'real-estate' => [
            'name' => 'Real Estate',
            'description' => 'Real estate website with property listings and agent profiles',
            'icon' => 'home',
            'color' => '#795548',
            'features' => [
                'property_listings',
                'virtual_tours',
                'agent_profiles',
                'mortgage_calculator',
                'property_search',
                'saved_searches',
                'contact_forms',
                'market_analysis',
            ],
            'dependencies' => [
                'auth',
                'roles',
                'media',
                'notifications',
                'payment',
                'calendar',
                'chat',
                'analytics',
            ],
        ],
        'restaurant' => [
            'name' => 'Restaurant',
            'description' => 'Restaurant website with menu management and reservations',
            'icon' => 'utensils',
            'color' => '#E91E63',
            'features' => [
                'menu_management',
                'table_reservation',
                'online_ordering',
                'delivery_tracking',
                'loyalty_program',
                'special_offers',
                'gallery_management',
                'customer_reviews',
            ],
            'dependencies' => [
                'auth',
                'roles',
                'media',
                'notifications',
                'payment',
                'calendar',
                'chat',
                'analytics',
            ],
        ],
        'fitness' => [
            'name' => 'Fitness',
            'description' => 'Fitness center website with class scheduling and member portal',
            'icon' => 'dumbbell',
            'color' => '#00BCD4',
            'features' => [
                'class_scheduling',
                'member_portal',
                'workout_tracking',
                'nutrition_planning',
                'trainer_profiles',
                'online_booking',
                'progress_tracking',
                'virtual_classes',
            ],
            'dependencies' => [
                'auth',
                'roles',
                'media',
                'notifications',
                'payment',
                'calendar',
                'chat',
                'analytics',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings that will be applied to all website types
    | unless overridden by specific type settings.
    |
    */

    'defaults' => [
        'features' => [
            'auth',
            'media',
            'notifications',
            'seo',
        ],
        'dependencies' => [
            'auth',
            'media',
            'notifications',
        ],
        'settings' => [
            'enable_registration' => true,
            'enable_social_login' => true,
            'enable_analytics' => true,
            'enable_seo' => true,
            'enable_cache' => true,
            'enable_logging' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Publishing
    |--------------------------------------------------------------------------
    |
    | Configure which assets should be published when installing a website type.
    |
    */

    'publish' => [
        'views' => true,
        'config' => true,
        'migrations' => true,
        'translations' => true,
        'assets' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Installation Settings
    |--------------------------------------------------------------------------
    |
    | Configure how website types should be installed and managed.
    |
    */

    'installation' => [
        'auto_migrate' => true,
        'auto_seed' => true,
        'backup_before_install' => true,
        'backup_before_uninstall' => true,
        'cleanup_after_uninstall' => true,
    ],
]; 