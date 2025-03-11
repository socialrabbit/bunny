<?php

namespace Kisalay\Bunny\WebsiteTypes;

class FitnessWebsite extends BaseWebsiteType
{
    public function install()
    {
        $this->addFeatures();
        $this->addDependencies();
        $this->publishAssets();
        $this->runMigrations();
        $this->updateConfig();
        $this->createDefaultData();
    }

    public function uninstall()
    {
        $this->rollbackMigrations();
        $this->removeAssets();
    }

    public function getFeatures()
    {
        return [
            'class_scheduling',
            'member_portal',
            'workout_tracking',
            'nutrition_planning',
            'trainer_profiles',
            'online_booking',
            'progress_tracking',
            'virtual_classes',
        ];
    }

    public function getDependencies()
    {
        return [
            'auth',
            'roles',
            'media',
            'notifications',
            'payment',
            'calendar',
            'chat',
            'analytics',
        ];
    }

    protected function addFeatures()
    {
        foreach ($this->getFeatures() as $feature) {
            $this->addFeature($feature);
        }
    }

    protected function addDependencies()
    {
        foreach ($this->getDependencies() as $dependency) {
            $this->addDependency($dependency);
        }
    }

    protected function createDefaultData()
    {
        $this->createDefaultPages();
        $this->createDefaultClasses();
        $this->createDefaultTrainers();
        $this->createDefaultMemberships();
        $this->createDefaultWorkouts();
        $this->createDefaultNutritionPlans();
    }

    protected function createDefaultPages()
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => 'Welcome to our fitness center...',
            ],
            [
                'title' => 'Classes',
                'slug' => 'classes',
                'content' => 'Browse our fitness classes...',
            ],
            [
                'title' => 'Trainers',
                'slug' => 'trainers',
                'content' => 'Meet our expert trainers...',
            ],
            [
                'title' => 'Memberships',
                'slug' => 'memberships',
                'content' => 'Choose your membership plan...',
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'content' => 'Get in touch...',
            ],
        ];

        foreach ($pages as $page) {
            \App\Models\Page::create($page);
        }
    }

    protected function createDefaultClasses()
    {
        $classes = [
            [
                'name' => 'Yoga Flow',
                'slug' => 'yoga-flow',
                'description' => 'Beginner-friendly yoga class focusing on flow and flexibility',
                'duration' => '60 minutes',
                'level' => 'beginner',
                'trainer_id' => 1,
                'capacity' => 20,
                'schedule' => [
                    'monday' => ['09:00', '17:00'],
                    'wednesday' => ['09:00', '17:00'],
                    'friday' => ['09:00', '17:00'],
                ],
                'price' => 15.00,
                'image' => 'classes/yoga.jpg',
            ],
            [
                'name' => 'HIIT Training',
                'slug' => 'hiit-training',
                'description' => 'High-intensity interval training for maximum calorie burn',
                'duration' => '45 minutes',
                'level' => 'advanced',
                'trainer_id' => 2,
                'capacity' => 15,
                'schedule' => [
                    'tuesday' => ['07:00', '18:00'],
                    'thursday' => ['07:00', '18:00'],
                    'saturday' => ['10:00'],
                ],
                'price' => 20.00,
                'image' => 'classes/hiit.jpg',
            ],
        ];

        foreach ($classes as $class) {
            \App\Models\FitnessClass::create($class);
        }
    }

    protected function createDefaultTrainers()
    {
        $trainers = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'specialization' => 'Yoga',
                'bio' => 'Certified yoga instructor with 10 years of experience',
                'image' => 'trainers/sarah.jpg',
                'certifications' => ['RYT-500', 'Pilates Certified'],
                'experience_years' => 10,
                'languages' => ['English', 'Spanish'],
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@example.com',
                'specialization' => 'HIIT',
                'bio' => 'Personal trainer specializing in high-intensity workouts',
                'image' => 'trainers/mike.jpg',
                'certifications' => ['NASM CPT', 'CrossFit Level 2'],
                'experience_years' => 8,
                'languages' => ['English'],
            ],
        ];

        foreach ($trainers as $trainer) {
            \App\Models\Trainer::create($trainer);
        }
    }

    protected function createDefaultMemberships()
    {
        $memberships = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Access to gym facilities and group classes',
                'price' => 49.99,
                'duration' => 'monthly',
                'features' => [
                    'gym_access',
                    'group_classes',
                    'locker_room',
                    'free_parking',
                ],
                'image' => 'memberships/basic.jpg',
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Full access to all facilities and personal training',
                'price' => 99.99,
                'duration' => 'monthly',
                'features' => [
                    'gym_access',
                    'group_classes',
                    'personal_training',
                    'nutrition_planning',
                    'spa_access',
                ],
                'image' => 'memberships/premium.jpg',
            ],
        ];

        foreach ($memberships as $membership) {
            \App\Models\Membership::create($membership);
        }
    }

    protected function createDefaultWorkouts()
    {
        $workouts = [
            [
                'name' => 'Full Body Strength',
                'slug' => 'full-body-strength',
                'description' => 'Complete full body workout focusing on strength',
                'duration' => '45 minutes',
                'level' => 'intermediate',
                'equipment' => ['dumbbells', 'barbell', 'kettlebell'],
                'exercises' => [
                    [
                        'name' => 'Squats',
                        'sets' => 3,
                        'reps' => '12-15',
                        'rest' => '60 seconds',
                    ],
                    [
                        'name' => 'Deadlifts',
                        'sets' => 3,
                        'reps' => '10-12',
                        'rest' => '90 seconds',
                    ],
                ],
                'image' => 'workouts/full-body.jpg',
            ],
            [
                'name' => 'Cardio Blast',
                'slug' => 'cardio-blast',
                'description' => 'High-intensity cardio workout',
                'duration' => '30 minutes',
                'level' => 'advanced',
                'equipment' => ['treadmill', 'rower', 'jump rope'],
                'exercises' => [
                    [
                        'name' => 'Sprint Intervals',
                        'sets' => 8,
                        'duration' => '30 seconds',
                        'rest' => '60 seconds',
                    ],
                    [
                        'name' => 'Mountain Climbers',
                        'sets' => 4,
                        'duration' => '45 seconds',
                        'rest' => '30 seconds',
                    ],
                ],
                'image' => 'workouts/cardio.jpg',
            ],
        ];

        foreach ($workouts as $workout) {
            \App\Models\Workout::create($workout);
        }
    }

    protected function createDefaultNutritionPlans()
    {
        $plans = [
            [
                'name' => 'Weight Loss',
                'slug' => 'weight-loss',
                'description' => 'Balanced meal plan for sustainable weight loss',
                'calories' => 1800,
                'duration' => '30 days',
                'meals' => [
                    [
                        'type' => 'breakfast',
                        'calories' => 400,
                        'foods' => ['oatmeal', 'fruits', 'nuts'],
                    ],
                    [
                        'type' => 'lunch',
                        'calories' => 500,
                        'foods' => ['grilled chicken', 'brown rice', 'vegetables'],
                    ],
                    [
                        'type' => 'dinner',
                        'calories' => 500,
                        'foods' => ['salmon', 'quinoa', 'salad'],
                    ],
                ],
                'image' => 'nutrition/weight-loss.jpg',
            ],
            [
                'name' => 'Muscle Gain',
                'slug' => 'muscle-gain',
                'description' => 'High-protein meal plan for muscle building',
                'calories' => 2500,
                'duration' => '30 days',
                'meals' => [
                    [
                        'type' => 'breakfast',
                        'calories' => 600,
                        'foods' => ['eggs', 'toast', 'protein shake'],
                    ],
                    [
                        'type' => 'lunch',
                        'calories' => 700,
                        'foods' => ['steak', 'sweet potato', 'broccoli'],
                    ],
                    [
                        'type' => 'dinner',
                        'calories' => 700,
                        'foods' => ['chicken', 'rice', 'vegetables'],
                    ],
                ],
                'image' => 'nutrition/muscle-gain.jpg',
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\NutritionPlan::create($plan);
        }
    }

    protected function removeAssets()
    {
        $paths = [
            resource_path("views/vendor/bunny/{$this->type}"),
            config_path("bunny/{$this->type}.php"),
            database_path("migrations/vendor/bunny/{$this->type}"),
            resource_path("lang/vendor/bunny/{$this->type}"),
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                File::deleteDirectory($path);
            }
        }
    }
} 