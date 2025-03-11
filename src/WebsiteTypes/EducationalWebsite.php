<?php

namespace Kisalay\Bunny\WebsiteTypes;

class EducationalWebsite extends BaseWebsiteType
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
            'course_management',
            'student_portal',
            'assignment_system',
            'progress_tracking',
            'quiz_system',
            'resource_library',
            'discussion_forums',
            'certificate_generation',
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
        $this->createDefaultCourses();
        $this->createDefaultDepartments();
        $this->createDefaultTeachers();
        $this->createDefaultResources();
        $this->createDefaultQuizzes();
    }

    protected function createDefaultPages()
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => 'Welcome to our educational platform...',
            ],
            [
                'title' => 'Courses',
                'slug' => 'courses',
                'content' => 'Browse our courses...',
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => 'Learn about our institution...',
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

    protected function createDefaultCourses()
    {
        $courses = [
            [
                'title' => 'Introduction to Programming',
                'slug' => 'intro-programming',
                'description' => 'Learn the basics of programming...',
                'department_id' => 1,
                'teacher_id' => 1,
                'duration' => '12 weeks',
                'level' => 'beginner',
                'price' => 99.99,
                'image' => 'courses/programming.jpg',
                'status' => 'active',
            ],
            [
                'title' => 'Web Development Fundamentals',
                'slug' => 'web-dev-fundamentals',
                'description' => 'Master web development basics...',
                'department_id' => 1,
                'teacher_id' => 2,
                'duration' => '16 weeks',
                'level' => 'intermediate',
                'price' => 149.99,
                'image' => 'courses/web-dev.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($courses as $course) {
            \App\Models\Course::create($course);
        }
    }

    protected function createDefaultDepartments()
    {
        $departments = [
            [
                'name' => 'Computer Science',
                'slug' => 'computer-science',
                'description' => 'Department of Computer Science',
                'head_id' => 1,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Department of Business',
                'head_id' => 2,
            ],
            [
                'name' => 'Arts & Humanities',
                'slug' => 'arts-humanities',
                'description' => 'Department of Arts & Humanities',
                'head_id' => 3,
            ],
        ];

        foreach ($departments as $department) {
            \App\Models\Department::create($department);
        }
    }

    protected function createDefaultTeachers()
    {
        $teachers = [
            [
                'name' => 'Dr. John Smith',
                'email' => 'john.smith@example.com',
                'department_id' => 1,
                'specialization' => 'Programming',
                'bio' => 'Experienced programming instructor...',
                'image' => 'teachers/john-smith.jpg',
            ],
            [
                'name' => 'Dr. Jane Doe',
                'email' => 'jane.doe@example.com',
                'department_id' => 1,
                'specialization' => 'Web Development',
                'bio' => 'Web development expert...',
                'image' => 'teachers/jane-doe.jpg',
            ],
        ];

        foreach ($teachers as $teacher) {
            \App\Models\Teacher::create($teacher);
        }
    }

    protected function createDefaultResources()
    {
        $resources = [
            [
                'title' => 'Programming Basics Guide',
                'type' => 'pdf',
                'description' => 'Comprehensive guide to programming basics',
                'file_path' => 'resources/programming-basics.pdf',
                'course_id' => 1,
                'downloadable' => true,
            ],
            [
                'title' => 'Web Development Video Series',
                'type' => 'video',
                'description' => 'Video tutorials for web development',
                'file_path' => 'resources/web-dev-series.mp4',
                'course_id' => 2,
                'downloadable' => false,
            ],
        ];

        foreach ($resources as $resource) {
            \App\Models\Resource::create($resource);
        }
    }

    protected function createDefaultQuizzes()
    {
        $quizzes = [
            [
                'title' => 'Programming Fundamentals Quiz',
                'description' => 'Test your programming knowledge',
                'course_id' => 1,
                'duration' => 30,
                'passing_score' => 70,
                'total_questions' => 10,
            ],
            [
                'title' => 'Web Development Quiz',
                'description' => 'Test your web development knowledge',
                'course_id' => 2,
                'duration' => 45,
                'passing_score' => 75,
                'total_questions' => 15,
            ],
        ];

        foreach ($quizzes as $quiz) {
            \App\Models\Quiz::create($quiz);
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