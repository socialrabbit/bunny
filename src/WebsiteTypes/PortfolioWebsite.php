<?php

namespace Kisalay\Bunny\WebsiteTypes;

class PortfolioWebsite extends BaseWebsiteType
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
            'project_showcase',
            'client_testimonials',
            'blog_integration',
            'contact_forms',
            'gallery_management',
            'resume_builder',
            'skills_showcase',
            'achievement_timeline',
        ];
    }

    public function getDependencies()
    {
        return [
            'auth',
            'media',
            'notifications',
            'seo',
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
        $this->createDefaultProjects();
        $this->createDefaultSkills();
        $this->createDefaultTestimonials();
        $this->createDefaultTimeline();
    }

    protected function createDefaultPages()
    {
        $pages = [
            [
                'title' => 'About Me',
                'slug' => 'about-me',
                'content' => 'Welcome to my portfolio...',
            ],
            [
                'title' => 'Projects',
                'slug' => 'projects',
                'content' => 'Check out my work...',
            ],
            [
                'title' => 'Skills',
                'slug' => 'skills',
                'content' => 'My expertise...',
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

    protected function createDefaultProjects()
    {
        $projects = [
            [
                'title' => 'Project 1',
                'description' => 'Description of project 1...',
                'category' => 'Web Development',
                'technologies' => ['Laravel', 'Vue.js', 'Tailwind CSS'],
                'image' => 'projects/project-1.jpg',
                'url' => 'https://project1.com',
                'github_url' => 'https://github.com/username/project1',
            ],
            [
                'title' => 'Project 2',
                'description' => 'Description of project 2...',
                'category' => 'Mobile App',
                'technologies' => ['React Native', 'Node.js', 'MongoDB'],
                'image' => 'projects/project-2.jpg',
                'url' => 'https://project2.com',
                'github_url' => 'https://github.com/username/project2',
            ],
        ];

        foreach ($projects as $project) {
            \App\Models\Project::create($project);
        }
    }

    protected function createDefaultSkills()
    {
        $skills = [
            [
                'name' => 'Web Development',
                'category' => 'Programming',
                'level' => 90,
                'description' => 'Full-stack web development expertise',
            ],
            [
                'name' => 'UI/UX Design',
                'category' => 'Design',
                'level' => 85,
                'description' => 'User interface and experience design',
            ],
            [
                'name' => 'Mobile Development',
                'category' => 'Programming',
                'level' => 80,
                'description' => 'Cross-platform mobile app development',
            ],
        ];

        foreach ($skills as $skill) {
            \App\Models\Skill::create($skill);
        }
    }

    protected function createDefaultTestimonials()
    {
        $testimonials = [
            [
                'client_name' => 'John Smith',
                'company' => 'Tech Corp',
                'content' => 'Great work on our project!',
                'rating' => 5,
                'image' => 'testimonials/client1.jpg',
            ],
            [
                'client_name' => 'Jane Doe',
                'company' => 'Design Studio',
                'content' => 'Excellent collaboration!',
                'rating' => 5,
                'image' => 'testimonials/client2.jpg',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            \App\Models\Testimonial::create($testimonial);
        }
    }

    protected function createDefaultTimeline()
    {
        $timeline = [
            [
                'title' => 'Started Freelancing',
                'date' => '2020-01-01',
                'description' => 'Began working as a freelance developer',
                'type' => 'achievement',
            ],
            [
                'title' => 'First Major Project',
                'date' => '2020-06-15',
                'description' => 'Completed first large-scale project',
                'type' => 'achievement',
            ],
            [
                'title' => 'Certification',
                'date' => '2021-03-20',
                'description' => 'Obtained AWS Certified Developer certification',
                'type' => 'education',
            ],
        ];

        foreach ($timeline as $event) {
            \App\Models\Timeline::create($event);
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