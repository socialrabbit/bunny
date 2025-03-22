<?php

namespace Kisalay\Bunny\WebsiteTypes;

class BusinessWebsite extends BaseWebsiteType
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
            'service_showcase',
            'team_profiles',
            'case_studies',
            'client_portal',
            'appointment_booking',
            'document_management',
            'newsletter_system',
            'contact_management',
        ];
    }

    public function getDependencies()
    {
        return [
            'auth',
            'roles',
            'media',
            'notifications',
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
        // Create default business data
        $this->createDefaultPages();
        $this->createDefaultServices();
        $this->createDefaultTeam();
        $this->createDefaultCaseStudies();
        $this->createDefaultTestimonials();
    }

    protected function createDefaultPages()
    {
        // Create default pages
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => 'Welcome to our company...',
            ],
            [
                'title' => 'Services',
                'slug' => 'services',
                'content' => 'Our services...',
            ],
            [
                'title' => 'Team',
                'slug' => 'team',
                'content' => 'Meet our team...',
            ],
            [
                'title' => 'Case Studies',
                'slug' => 'case-studies',
                'content' => 'Our success stories...',
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

    protected function createDefaultServices()
    {
        // Create default services
        $services = [
            [
                'title' => 'Service 1',
                'description' => 'Description of service 1...',
                'icon' => 'service1',
            ],
            [
                'title' => 'Service 2',
                'description' => 'Description of service 2...',
                'icon' => 'service2',
            ],
            [
                'title' => 'Service 3',
                'description' => 'Description of service 3...',
                'icon' => 'service3',
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }

    protected function createDefaultTeam()
    {
        // Create default team members
        $team = [
            [
                'name' => 'John Doe',
                'position' => 'CEO',
                'bio' => 'John is our CEO...',
                'image' => 'team/john-doe.jpg',
            ],
            [
                'name' => 'Jane Smith',
                'position' => 'CTO',
                'bio' => 'Jane is our CTO...',
                'image' => 'team/jane-smith.jpg',
            ],
            [
                'name' => 'Mike Johnson',
                'position' => 'COO',
                'bio' => 'Mike is our COO...',
                'image' => 'team/mike-johnson.jpg',
            ],
        ];

        foreach ($team as $member) {
            \App\Models\TeamMember::create($member);
        }
    }

    protected function createDefaultCaseStudies()
    {
        // Create default case studies
        $caseStudies = [
            [
                'title' => 'Case Study 1',
                'description' => 'Description of case study 1...',
                'client' => 'Client 1',
                'date' => now(),
                'image' => 'case-studies/case-study-1.jpg',
            ],
            [
                'title' => 'Case Study 2',
                'description' => 'Description of case study 2...',
                'client' => 'Client 2',
                'date' => now(),
                'image' => 'case-studies/case-study-2.jpg',
            ],
            [
                'title' => 'Case Study 3',
                'description' => 'Description of case study 3...',
                'client' => 'Client 3',
                'date' => now(),
                'image' => 'case-studies/case-study-3.jpg',
            ],
        ];

        foreach ($caseStudies as $caseStudy) {
            \App\Models\CaseStudy::create($caseStudy);
        }
    }

    protected function createDefaultTestimonials()
    {
        $testimonials = [
            [
                'client_name' => 'Alice Johnson',
                'company' => 'Tech Solutions',
                'content' => 'Outstanding service and support!',
                'rating' => 5,
                'image' => 'testimonials/client1.jpg',
            ],
            [
                'client_name' => 'Bob Smith',
                'company' => 'Creative Agency',
                'content' => 'Highly recommend their expertise.',
                'rating' => 5,
                'image' => 'testimonials/client2.jpg',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            \App\Models\Testimonial::create($testimonial);
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