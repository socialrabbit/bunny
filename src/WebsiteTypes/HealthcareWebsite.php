<?php

namespace Kisalay\Bunny\WebsiteTypes;

class HealthcareWebsite extends BaseWebsiteType
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
            'patient_portal',
            'appointment_scheduling',
            'medical_records',
            'prescription_management',
            'telemedicine_integration',
            'health_blog',
            'insurance_verification',
            'emergency_contact',
        ];
    }

    public function getDependencies()
    {
        return [
            'auth',
            'roles',
            'media',
            'notifications',
            'calendar',
            'chat',
            'payment',
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
        $this->createDefaultDoctors();
        $this->createDefaultDepartments();
        $this->createDefaultServices();
        $this->createDefaultBlogPosts();
        $this->createDefaultInsuranceProviders();
    }

    protected function createDefaultPages()
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => 'Welcome to our healthcare center...',
            ],
            [
                'title' => 'Services',
                'slug' => 'services',
                'content' => 'Our healthcare services...',
            ],
            [
                'title' => 'Doctors',
                'slug' => 'doctors',
                'content' => 'Meet our medical team...',
            ],
            [
                'title' => 'Appointments',
                'slug' => 'appointments',
                'content' => 'Schedule your appointment...',
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

    protected function createDefaultDoctors()
    {
        $doctors = [
            [
                'name' => 'Dr. John Smith',
                'email' => 'john.smith@example.com',
                'department_id' => 1,
                'specialization' => 'Cardiology',
                'bio' => 'Experienced cardiologist...',
                'image' => 'doctors/john-smith.jpg',
                'availability' => [
                    'monday' => ['09:00-17:00'],
                    'wednesday' => ['09:00-17:00'],
                    'friday' => ['09:00-17:00'],
                ],
            ],
            [
                'name' => 'Dr. Jane Doe',
                'email' => 'jane.doe@example.com',
                'department_id' => 2,
                'specialization' => 'Pediatrics',
                'bio' => 'Pediatric specialist...',
                'image' => 'doctors/jane-doe.jpg',
                'availability' => [
                    'tuesday' => ['09:00-17:00'],
                    'thursday' => ['09:00-17:00'],
                    'saturday' => ['09:00-13:00'],
                ],
            ],
        ];

        foreach ($doctors as $doctor) {
            \App\Models\Doctor::create($doctor);
        }
    }

    protected function createDefaultDepartments()
    {
        $departments = [
            [
                'name' => 'Cardiology',
                'slug' => 'cardiology',
                'description' => 'Heart and cardiovascular care',
                'head_id' => 1,
            ],
            [
                'name' => 'Pediatrics',
                'slug' => 'pediatrics',
                'description' => 'Child healthcare services',
                'head_id' => 2,
            ],
            [
                'name' => 'Orthopedics',
                'slug' => 'orthopedics',
                'description' => 'Bone and joint care',
                'head_id' => 3,
            ],
        ];

        foreach ($departments as $department) {
            \App\Models\Department::create($department);
        }
    }

    protected function createDefaultServices()
    {
        $services = [
            [
                'name' => 'General Checkup',
                'slug' => 'general-checkup',
                'description' => 'Comprehensive health examination',
                'department_id' => 1,
                'duration' => '60 minutes',
                'price' => 100.00,
                'image' => 'services/checkup.jpg',
            ],
            [
                'name' => 'Telemedicine Consultation',
                'slug' => 'telemedicine',
                'description' => 'Online medical consultation',
                'department_id' => 1,
                'duration' => '30 minutes',
                'price' => 50.00,
                'image' => 'services/telemedicine.jpg',
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }

    protected function createDefaultBlogPosts()
    {
        $posts = [
            [
                'title' => 'Tips for a Healthy Heart',
                'slug' => 'healthy-heart-tips',
                'content' => 'Learn how to maintain a healthy heart...',
                'author_id' => 1,
                'category' => 'Cardiology',
                'image' => 'blog/heart-health.jpg',
                'status' => 'published',
            ],
            [
                'title' => 'Childhood Vaccination Guide',
                'slug' => 'vaccination-guide',
                'content' => 'Everything you need to know about childhood vaccinations...',
                'author_id' => 2,
                'category' => 'Pediatrics',
                'image' => 'blog/vaccination.jpg',
                'status' => 'published',
            ],
        ];

        foreach ($posts as $post) {
            \App\Models\BlogPost::create($post);
        }
    }

    protected function createDefaultInsuranceProviders()
    {
        $providers = [
            [
                'name' => 'HealthCare Plus',
                'code' => 'HCP001',
                'description' => 'Comprehensive health insurance',
                'contact_info' => [
                    'phone' => '1-800-123-4567',
                    'email' => 'support@healthcareplus.com',
                ],
                'verification_url' => 'https://verify.healthcareplus.com',
            ],
            [
                'name' => 'MediCare Pro',
                'code' => 'MCP001',
                'description' => 'Professional medical insurance',
                'contact_info' => [
                    'phone' => '1-800-987-6543',
                    'email' => 'support@medicarepro.com',
                ],
                'verification_url' => 'https://verify.medicarepro.com',
            ],
        ];

        foreach ($providers as $provider) {
            \App\Models\InsuranceProvider::create($provider);
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